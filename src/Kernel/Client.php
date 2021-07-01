<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel;

use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Contracts\AccessTokenAwareHttpClient as AccessTokenAwareHttpClientInterface;
use EasyWeChat\Kernel\Support\Str;
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
class Client implements AccessTokenAwareHttpClientInterface
{
    use DecoratorTrait;

    protected string $uri = '/';
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

    public function withUri(string $uri): Client
    {
        $clone = clone $this;

        if (\str_starts_with($uri, 'http://') || \str_starts_with($uri, 'https://')) {
            $clone->uri = $uri;
        } else {
            $uri = Str::kebab($uri);
            $clone->uri = \sprintf('/%s/%s', \trim($this->uri, '/'), \trim($uri, '/'));
        }

        return $clone;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function __get(string | int $name)
    {
        return $this->withUri(\strval($name));
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function __call(string $name, array $arguments)
    {
        if (\in_array(\strtoupper($name), ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])) {
            return $this->callWithShortcuts(\strtoupper($name), ...$arguments);
        }

        return \call_user_func_array([$this->client, $name], $arguments);
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

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    protected function callWithShortcuts(string $method, string | array $uri = [], array $options = []): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        if (\is_string($uri)) {
            $uri = $this->withUri($uri)->getUri();
        } else {
            $options = $uri;
            $uri = $this->getUri();
        }

        return $this->request(\strtoupper($method), $uri, $options);
    }
}
