<?php

declare(strict_types=1);

namespace EasyWeChat\Pay;

use EasyWeChat\Kernel\Support\PrivateKey;
use EasyWeChat\Kernel\Support\PublicKey;
use EasyWeChat\Kernel\Support\UserAgent;
use EasyWeChat\Kernel\Traits\HttpClientMethods;
use EasyWeChat\Kernel\Traits\MockableHttpClient;
use Mockery\Mock;
use Nyholm\Psr7\Uri;
use Symfony\Component\HttpClient\DecoratorTrait;
use Symfony\Component\HttpClient\HttpClient as SymfonyHttpClient;
use Symfony\Component\HttpClient\HttpClientTrait;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @method ResponseInterface get(string $uri, array $options = [])
 * @method ResponseInterface post(string $uri, array $options = [])
 * @method ResponseInterface put(string $uri, array $options = [])
 * @method ResponseInterface patch(string $uri, array $options = [])
 * @method ResponseInterface delete(string $uri, array $options = [])
 */
class Client implements HttpClientInterface
{
    use DecoratorTrait {
        DecoratorTrait::withOptions insteadof HttpClientTrait;}
    use HttpClientTrait;
    use HttpClientMethods;
    use MockableHttpClient;

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
    ];

    /**
     * @param  array<string, mixed> $defaultOptions
     */
    public function __construct(protected Merchant $merchant, ?HttpClientInterface $client = null, array $defaultOptions = [])
    {
        $this->defaultOptions = array_merge(self::OPTIONS_DEFAULTS, $this->defaultOptions);

        if (!empty($defaultOptions)) {
            [, $this->defaultOptions] = self::prepareRequest(null, null, $defaultOptions, $this->defaultOptions);
        }

        $this->client = ($client ?? SymfonyHttpClient::create())->withOptions($this->defaultOptions);
    }

    /**
     * @param  array<array-key, mixed>  $options
     *
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Exception
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $options['headers']['User-Agent'] = UserAgent::create([$options['headers']['User-Agent'] ?? '']);

        if ($this->isV3Request($url)) {
            [, $options] = $this->prepareRequest($method, $url, $options, $this->defaultOptions, true);
            $options['headers']['Authorization'] = $this->createSignature($method, $url, $options);
        } elseif (!empty($options['json']) && \is_array($options['json'])) {
            $options['json'] = $this->attachLegacySignature($options['json']);
        } elseif (!empty($options['body']) && \is_array($options['body'])) {
            $options['body'] = $this->attachLegacySignature($options['body']);
        }

        return $this->client->request($method, $url, $options);
    }

    protected function isV3Request(string $url): bool
    {
        $uri = new Uri($url);

        foreach (self::V3_URI_PREFIXES as $prefix) {
            if (\str_starts_with('/' . ltrim($uri->getPath(), '/'), $prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->client->$name(...$arguments);
    }

    /**
     * @param  array<string, mixed>  $options
     *
     * @throws \Exception
     */
    protected function createSignature(string $method, string $url, array $options): string
    {
        return (new Signature($this->merchant))->createHeader($method, $url, $options);
    }

    /**
     * @param array<string, mixed>  $body
     * @return array<string, mixed>
     */
    protected function attachLegacySignature(array $body): array
    {
        return (new LegacySignature($this->merchant))->sign($body);
    }

    public static function createMockClient(MockHttpClient $mockHttpClient): HttpClientInterface|Mock
    {
        $mockMerchant = new Merchant(
            'mch_id',
            /** @phpstan-ignore-next-line*/
            \Mockery::mock(PrivateKey::class),
            /** @phpstan-ignore-next-line*/
            \Mockery::mock(PublicKey::class),
            'mock-v3-key',
            'mock-v2-key',
        );

        return \Mockery::mock(static::class, [$mockMerchant, $mockHttpClient])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
    }
}
