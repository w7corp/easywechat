<?php

namespace EasyWeChat\Pay;

use EasyWeChat\Kernel\Support\UserAgent;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\HttpClientTrait;
use Symfony\Component\HttpClient\Psr18Client;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

class Client implements HttpClientInterface
{
    use HttpClientTrait;

    protected HttpClientInterface $client;

    public const V3_URI_PREFIXES = [
        '/v3/',
        '/sandbox/v3/',
        '/hk/v3/',
    ];

    public function __construct(protected Merchant $merchant, ?HttpClientInterface $client = null)
    {
        $this->client = $client ?? HttpClient::create();
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $request = (new Psr18Client())->createRequest($method, $url);

        $options['headers']['User-Agent'] = UserAgent::create([$options['headers']['User-Agent'] ?? '']);

        if ($this->isV3Request($request)) {
            [$url, $options] = self::prepareRequest($method, $url, $options);

            if (!empty($options['body'])) {
                $request->withBody($options['body']);
            }

            $options['headers']['Authorization'] = (new Signature($this->merchant))->createHeader($request);
        } elseif (!empty($options['json'])) {
            $options['json'] = (new LegacySignature($this->merchant))->sign($options['json']);
        } elseif (!empty($options['body']) && \is_array($options['body'])) {
            $options['body'] = (new LegacySignature($this->merchant))->sign($options['body']);
        }

        return $this->client->request($method, $url, $options);
    }

    public function __call(string $name, array $arguments)
    {
        return \call_user_func_array([$this->client, $name], $arguments);
    }

    public function isV3Request(RequestInterface $request): bool
    {
        foreach (self::V3_URI_PREFIXES as $prefix) {
            if (\str_starts_with($request->getUri(), $prefix)) {
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
