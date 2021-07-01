<?php

declare(strict_types=1);

namespace EasyWeChat\Pay;

use EasyWeChat\Kernel\Contracts\ChainableHttpClient as ChainableHttpClientInterface;
use EasyWeChat\Kernel\Support\UserAgent;
use EasyWeChat\Kernel\Traits\ChainableHttpClient;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpClient\HttpClient as SymfonyHttpClient;
use Symfony\Component\HttpClient\HttpClientTrait;
use Symfony\Component\HttpClient\Psr18Client;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

class MerchantAwareHttpClient implements HttpClientInterface, ChainableHttpClientInterface
{
    use HttpClientTrait;
    use ChainableHttpClient;

    protected HttpClientInterface $client;

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

    public function __construct(protected Merchant $merchant, ?HttpClientInterface $client = null, array $defaultOptions = [])
    {
        $this->client = ($client ?? SymfonyHttpClient::create())->withOptions(\array_merge($this->defaultOptions, $defaultOptions));
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $request = (new Psr18Client())->createRequest($method, $url);

        $options['headers']['User-Agent'] = UserAgent::create([$options['headers']['User-Agent'] ?? '']);

        if ($this->isV3Request($request)) {
            if (!empty($options['body'])) {
                $request = $request->withBody(Stream::create($options['body']));
            }

            $options['headers']['Authorization'] = (new Signature($this->merchant))->createHeader($request);
        } elseif (!empty($options['json'])) {
            $options['json'] = (new LegacySignature($this->merchant))->sign($options['json']);
        } elseif (!empty($options['body']) && \is_array($options['body'])) {
            $options['body'] = (new LegacySignature($this->merchant))->sign($options['body']);
        }

        return $this->client->request($method, $url, $options);
    }

    protected function isV3Request(RequestInterface $request): bool
    {
        foreach (self::V3_URI_PREFIXES as $prefix) {
            if (\str_starts_with($request->getUri()->getPath(), $prefix)) {
                return true;
            }
        }

        return false;
    }

    public function stream($responses, float $timeout = null): ResponseStreamInterface
    {
        return $this->client->stream($responses, $timeout);
    }
}
