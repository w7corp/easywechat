<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel;

use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Contracts\AccessTokenAwareHttpClient as AccessTokenAwareHttpClientInterface;
use EasyWeChat\Kernel\Traits\HttpClientMethods;
use EasyWeChat\Kernel\Traits\MockableHttpClient;
use Symfony\Component\HttpClient\DecoratorTrait;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Client implements AccessTokenAwareHttpClientInterface
{
    use DecoratorTrait;
    use HttpClientMethods;
    use MockableHttpClient;

    public function __construct(
        ?HttpClientInterface $client = null,
        protected ?AccessTokenInterface $accessToken = null,
    ) {
        $this->client = $client ?? HttpClient::create();
    }

    public function withAccessToken(AccessTokenInterface $accessToken): static
    {
        $clone = clone $this;

        $clone->accessToken = $accessToken;

        return $clone;
    }

    /**
     * @param array<string, mixed> $options
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function request(string $method, string $url, array $options = []): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        if ($this->accessToken) {
            $options['query'] = \array_merge($options['query'] ?? [], $this->accessToken->toQuery());
        }

        return $this->client->request($method, ltrim($url, '/'), $options);
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->client->$name(...$arguments);
    }

    public static function createMockClient(MockHttpClient $mockHttpClient): HttpClientInterface
    {
        return new self($mockHttpClient);
    }
}
