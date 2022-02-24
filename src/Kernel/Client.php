<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel;

use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Contracts\AccessTokenAwareHttpClient as AccessTokenAwareHttpClientInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpClient\DecoratorTrait;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Client implements AccessTokenAwareHttpClientInterface
{
    use DecoratorTrait;

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
    public function get(string $url, array $options = []): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        return $this->request('GET', $url, $options);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function post(string $url, array $options = []): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        if (!\array_key_exists('body', $options) && !\array_key_exists('json', $options)) {
            $options['body'] = $options;
        }

        return $this->request('POST', $url, $options);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function patch(string $url, array $options = []): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        if (!\array_key_exists('body', $options) && !\array_key_exists('json', $options)) {
            $options['body'] = $options;
        }

        return $this->request('PATCH', $url, $options);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function put(string $url, array $options = []): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        if (!\array_key_exists('body', $options) && !\array_key_exists('json', $options)) {
            $options['body'] = $options;
        }

        return $this->request('PUT', $url, $options);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function delete(string $url, array $options = []): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        return $this->request('DELETE', $url, $options);
    }

    public function __call(string $name, array $arguments)
    {
        return \call_user_func_array([$this->client, $name], $arguments);
    }

    public static function mock(string $response = '', ?int $status = 200, ?string $contentType = 'application/json', array $headers = [], string $baseUri = 'https://example.com'): object
    {
        $mockResponse = new MockResponse(
            $response,
            array_merge([
                'http_code' => $status,
                'content_type' => $contentType,
            ], $headers)
        );

        return new class ($mockResponse, $baseUri) {
            use DecoratorTrait;

            public function __construct(public MockResponse $mockResponse, $baseUri)
            {
                $this->client = new Client(new MockHttpClient($this->mockResponse, $baseUri));
            }

            public function __call(string $name, array $arguments)
            {
                return \call_user_func_array([$this->client, $name], $arguments);
            }

            #[Pure]
            public function getRequestMethod()
            {
                return $this->mockResponse->getRequestMethod();
            }

            #[Pure]
            public function getRequestUrl()
            {
                return $this->mockResponse->getRequestUrl();
            }

            #[Pure]
            public function getRequestOptions()
            {
                return $this->mockResponse->getRequestOptions();
            }
        };
    }
}
