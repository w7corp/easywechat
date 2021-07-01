<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel;

use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Contracts\AccessTokenAwareHttpClient as AccessTokenAwareHttpClientInterface;
use EasyWeChat\Kernel\Contracts\ChainableHttpClient as ChainableHttpClientInterface;
use EasyWeChat\Kernel\Traits\ChainableHttpClient;
use Symfony\Component\HttpClient\DecoratorTrait;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @method \Symfony\Contracts\HttpClient\ResponseInterface get(string|array $uri = [], array $options = [])
 * @method \Symfony\Contracts\HttpClient\ResponseInterface post(string|array $uri = [], array $options = [])
 * @method \Symfony\Contracts\HttpClient\ResponseInterface patch(string|array $uri = [], array $options = [])
 * @method \Symfony\Contracts\HttpClient\ResponseInterface put(string|array $uri = [], array $options = [])
 * @method \Symfony\Contracts\HttpClient\ResponseInterface delete(string|array $uri = [], array $options = [])
 */
class Client implements AccessTokenAwareHttpClientInterface, ChainableHttpClientInterface
{
    use DecoratorTrait;
    use ChainableHttpClient;

    protected ?AccessTokenInterface $accessToken;

    public function withAccessToken(AccessTokenInterface $accessToken): static
    {
        $clone = clone $this;

        $clone->accessToken = $accessToken;

        return $clone;
    }

    public function __construct(
        ?HttpClientInterface $client = null,
        string $uri = '/',
        ?AccessTokenInterface $accessToken = null,
    ) {
        $this->uri = $uri;
        $this->client = $client ?? HttpClient::create();
        $this->accessToken = $accessToken;
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function request(string $method, string $url, array $options = []): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        if ($this->accessToken) {
            $options['query'] = \array_merge($options['query'] ?? [], $this->accessToken->toQuery());
        }

        return $this->client->request($method, ltrim($url, '/'), $options);
    }
}
