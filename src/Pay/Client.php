<?php

declare(strict_types=1);

namespace EasyWeChat\Pay;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\Form\File;
use EasyWeChat\Kernel\Form\Form;
use EasyWeChat\Kernel\HttpClient\HttpClientMethods;
use EasyWeChat\Kernel\HttpClient\RequestUtil;
use EasyWeChat\Kernel\HttpClient\RequestWithPresets;
use EasyWeChat\Kernel\HttpClient\Response;
use EasyWeChat\Kernel\Support\PrivateKey;
use EasyWeChat\Kernel\Support\PublicKey;
use EasyWeChat\Kernel\Support\UserAgent;
use EasyWeChat\Kernel\Support\Xml;
use EasyWeChat\Kernel\Traits\MockableHttpClient;
use Exception;
use Mockery;
use Mockery\Mock;
use Nyholm\Psr7\Uri;
use Symfony\Component\HttpClient\DecoratorTrait;
use Symfony\Component\HttpClient\HttpClient as SymfonyHttpClient;
use Symfony\Component\HttpClient\HttpClientTrait;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

use function is_array;
use function is_string;
use function str_starts_with;

/**
 * @method ResponseInterface get(string $uri, array $options = [])
 * @method ResponseInterface post(string $uri, array $options = [])
 * @method ResponseInterface put(string $uri, array $options = [])
 * @method ResponseInterface patch(string $uri, array $options = [])
 * @method ResponseInterface delete(string $uri, array $options = [])
 * @method HttpClientInterface withMchId(string $value = null)
 * @method HttpClientInterface withMchIdAs(string $key)
 */
class Client implements HttpClientInterface
{
    use DecoratorTrait {
        DecoratorTrait::withOptions insteadof HttpClientTrait;
    }
    use HttpClientMethods;
    use HttpClientTrait;
    use MockableHttpClient;
    use RequestWithPresets;

    /**
     * @var array<string, mixed>
     */
    protected array $defaultOptions = [
        'base_uri' => 'https://api.mch.weixin.qq.com/',
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
    ];

    public const V3_URI_PREFIXES = [
        '/v3/',
        '/sandbox/v3/',
        '/hk/v3/',
        '/global/v3/',
    ];

    protected bool $throw = true;

    /**
     * @param  array<string, mixed>  $defaultOptions
     */
    public function __construct(
        protected Merchant $merchant,
        ?HttpClientInterface $client = null,
        array $defaultOptions = []
    ) {
        $this->throw = (bool) ($defaultOptions['throw'] ?? true);

        $this->defaultOptions = array_merge(self::OPTIONS_DEFAULTS, $this->defaultOptions);

        if (! empty($defaultOptions)) {
            $defaultOptions = RequestUtil::formatDefaultOptions($defaultOptions);
            [, $this->defaultOptions] = self::prepareRequest(null, null, $defaultOptions, $this->defaultOptions);
        }

        $this->client = ($client ?? SymfonyHttpClient::create())->withOptions($this->defaultOptions);
    }

    /**
     * @param  array<string, array|mixed>  $options
     *
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        /** @var array{headers?:array<string, string>, xml?:array|string, body?:array|string} $options */
        if (empty($options['headers'])) {
            $options['headers'] = [];
        }

        $options = $this->mergeThenResetPrepends($options);

        $options['headers']['User-Agent'] = UserAgent::create();

        if ($this->isV3Request($url)) {
            [, $_options] = $this->prepareRequest($method, $url, $options, $this->defaultOptions, true);

            // 部分签名算法需要使用到 body 中额外的部分，所以交由前置逻辑自行完成
            if (empty($options['headers']['Authorization'])) {
                $options['headers']['Authorization'] = $this->createSignature($method, $url, $_options);
            }
        } else {
            // v2 全部为 xml 请求
            if (! empty($options['xml'])) {
                if (is_array($options['xml'])) {
                    $options['xml'] = Xml::build($this->attachLegacySignature($options['xml']));
                }

                if (! is_string($options['xml'])) {
                    throw new \InvalidArgumentException('The `xml` option must be a string or array.');
                }

                $options['body'] = $options['xml'];
                unset($options['xml']);
            }

            if (! empty($options['body']) && is_array($options['body'])) {
                $options['body'] = Xml::build($this->attachLegacySignature($options['body']));
            }

            if (! isset($options['headers']['Content-Type']) && ! isset($options['headers']['content-type'])) {
                $options['headers']['Content-Type'] = 'text/xml';
            }
        }

        // 合并通过 withHeader 和 withHeaders 设置的信息
        if (! empty($this->prependHeaders)) {
            $options['headers'] = array_merge($this->prependHeaders, $options['headers']);
        }

        return new Response(
            $this->client->request($method, $url, $options),
            failureJudge: $this->isV3Request($url) ? null : fn (Response $response) => $response->toArray()['return_code'] === 'FAIL' || $response->toArray()['result_code'] === 'FAIL',
            throw: $this->throw
        );
    }

    protected function isV3Request(string $url): bool
    {
        $uri = new Uri($url);

        foreach (self::V3_URI_PREFIXES as $prefix) {
            if (str_starts_with('/'.ltrim($uri->getPath(), '/'), $prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<int, mixed>  $arguments
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (\str_starts_with($name, 'with')) {
            return $this->handleMagicWithCall($name, $arguments[0] ?? null);
        }

        return $this->client->$name(...$arguments);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function uploadMedia(string $uri, string $pathOrContents, ?array $meta = null, ?string $filename = null): ResponseInterface
    {
        $isFile = is_file($pathOrContents);

        $meta = self::jsonEncode($meta ?? [
            'filename' => $isFile ? basename($pathOrContents) : $filename ?? 'file',
            'sha256' => $isFile ? hash_file('sha256', $pathOrContents) : hash('sha256', $pathOrContents),
        ]);

        $form = Form::create([
            'file' => File::from($pathOrContents),
            'meta' => new DataPart($meta, null, 'application/json'),
        ]);

        $options = $signatureOptions = $form->toOptions();

        $signatureOptions['body'] = $meta;

        $options['headers']['Authorization'] = $this->createSignature('POST', $uri, $signatureOptions);

        return $this->request('POST', $uri, $options);
    }

    /**
     * @param  array<string, mixed>  $options
     *
     * @throws Exception
     */
    protected function createSignature(string $method, string $url, array $options): string
    {
        return (new Signature($this->merchant))->createHeader($method, $url, $options);
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    protected function attachLegacySignature(array $body): array
    {
        return (new LegacySignature($this->merchant))->sign($body);
    }

    /**
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     */
    public static function createMockClient(MockHttpClient $mockHttpClient): HttpClientInterface|Mock
    {
        $mockMerchant = new Merchant(
            'mch_id',
            /** @phpstan-ignore-next-line */
            Mockery::mock(PrivateKey::class),
            /** @phpstan-ignore-next-line */
            Mockery::mock(PublicKey::class),
            'mock-v3-key',
            'mock-v2-key',
        );

        return Mockery::mock(static::class, [$mockMerchant, $mockHttpClient])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
    }
}
