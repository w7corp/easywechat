<?php

declare(strict_types=1);

namespace EasyWeChat\Pay;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
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
use function is_array;
use function is_string;
use Mockery;
use Mockery\Mock;
use Nyholm\Psr7\Uri;
use function str_starts_with;
use Symfony\Component\HttpClient\DecoratorTrait;
use Symfony\Component\HttpClient\HttpClient as SymfonyHttpClient;
use Symfony\Component\HttpClient\HttpClientTrait;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

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
    use HttpClientTrait;
    use HttpClientMethods;
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
            $defaultOptions = RequestUtil::formatDefaultOptions($this->defaultOptions);
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
        if (empty($options['headers'])) {
            $options['headers'] = [];
        }

        /** @phpstan-ignore-next-line */
        $options['headers']['User-Agent'] = UserAgent::create();

        if ($this->isV3Request($url)) {
            [, $options] = $this->prepareRequest($method, $url, $options, $this->defaultOptions, true);
            $options['headers']['Authorization'] = $this->createSignature($method, $url, $options);
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

            /** @phpstan-ignore-next-line */
            if (! isset($options['headers']['Content-Type']) && ! isset($options['headers']['content-type'])) {
                $options['headers']['Content-Type'] = 'text/xml'; /** @phpstan-ignore-line */
            }
        }

        // 合并通过 withHeader 和 withHeaders 设置的信息
        if (! empty($this->prependHeaders)) {
            $options['headers'] = array_merge($this->prependHeaders, $options['headers'] ?? []);
        }

        return new Response($this->client->request($method, $url, $options), throw: $this->throw);
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
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (\str_starts_with($name, 'with')) {
            return $this->handleMagicWithCall($name, $arguments[0] ?? null);
        }

        return $this->client->$name(...$arguments);
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
