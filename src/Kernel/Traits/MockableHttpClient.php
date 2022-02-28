<?php

namespace EasyWeChat\Kernel\Traits;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpClient\DecoratorTrait;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

trait MockableHttpClient
{
    public static function createMockClient(MockHttpClient $mockHttpClient): HttpClientInterface
    {
        return new self($mockHttpClient);
    }

    public static function mock(string $response = '', ?int $status = 200, array $headers = [], string $baseUri = 'https://example.com'): object
    {
        $mockResponse = new MockResponse(
            $response,
            array_merge([
                'http_code' => $status,
                'content_type' => 'application/json',
            ], $headers)
        );

        $client = self::createMockClient(new MockHttpClient($mockResponse, $baseUri));

        return new class ($client, $mockResponse) {
            use DecoratorTrait;

            public function __construct(HttpClientInterface $client, public MockResponse $mockResponse)
            {
                $this->client = $client;
            }

            public function __call(string $name, array $arguments)
            {
                return \call_user_func_array([$this->client, $name], $arguments);
            }

            #[Pure]
            public function getRequestMethod(): string
            {
                return $this->mockResponse->getRequestMethod();
            }

            #[Pure]
            public function getRequestUrl(): string
            {
                return $this->mockResponse->getRequestUrl();
            }

            #[Pure]
            public function getRequestOptions(): array
            {
                return $this->mockResponse->getRequestOptions();
            }
        };
    }
}
