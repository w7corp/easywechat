<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Support\UserAgent;
use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use Symfony\Component\HttpClient\HttpClient as SymfonyHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

trait AccessTokenAwareHttpClient
{
    protected HttpClientInterface $client;
    protected ?AccessTokenInterface $accessToken = null;

    public function __construct(
        ?HttpClientInterface $client = null,
        ?AccessTokenInterface $accessToken = null,
    ) {
        $this->accessToken = $accessToken;
        $this->client = $client ?? SymfonyHttpClient::create();
    }

    public function withOptions(array $options): self
    {
        $clone = clone $this;
        $clone->defaultOptions = \array_merge($this->defaultOptions, $options);

        return $clone;
    }

    public function withAccessToken(AccessTokenInterface $accessToken): static
    {
        $clone = clone $this;

        $clone->accessToken = $accessToken;

        return $clone;
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $options = \array_merge($this->defaultOptions, $options);

        $options['headers']['User-Agent'] = UserAgent::create([$options['headers']['User-Agent'] ?? '']);

        if ($this->accessToken) {
            $options['query'] = \array_merge($options['query'] ?? [], $this->accessToken->toQuery());
        }

        return $this->client->request($method, $url, $options);
    }

    public function __call(string $name, array $arguments)
    {
        return \call_user_func_array([$this->client, $name], $arguments);
    }

    public function stream($responses, float $timeout = null): ResponseStreamInterface
    {
        return $this->client->stream($responses, $timeout);
    }
}
