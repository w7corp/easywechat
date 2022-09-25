<?php

namespace EasyWeChat\Tests\Kernel\HttpClient;

use EasyWeChat\Kernel\HttpClient\RetryableClient;
use EasyWeChat\Tests\TestCase;
use Symfony\Component\HttpClient\DecoratorTrait;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use Symfony\Component\HttpClient\RetryableHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RetryableClientTest extends TestCase
{
    public function test_it_can_retry_with_default_config()
    {
        $client = new DummyClientForRetryableClientTest([
            new MockResponse('', ['http_code' => 500]),
            new MockResponse('', ['http_code' => 500]),
            new MockResponse('', ['http_code' => 500]),
            new MockResponse('', ['http_code' => 200]),
        ]);

        $this->assertInstanceOf(HttpClientInterface::class, $client->getClient());
        $this->assertNotInstanceOf(RetryableHttpClient::class, $client->getClient());

        // No retry
        $response = $client->request('GET', 'http://foo.com');
        $this->assertSame(500, $response->getStatusCode());

        // Retry
        $client->retry(['delay' => 10]);
        $this->assertInstanceOf(RetryableHttpClient::class, $client->getClient());

        // default retry 3 times
        $response = $client->request('GET', 'http://foo.com');
        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_it_can_retry_with_custom_strategy()
    {
        $client = new DummyClientForRetryableClientTest([
            new MockResponse('', ['http_code' => 500]),
            new MockResponse('', ['http_code' => 500]),
            new MockResponse('', ['http_code' => 500]),
            new MockResponse('', ['http_code' => 200]),
        ]);

        $response = $client->request('GET', 'https://easywechat.com');

        $this->assertNull($response->getInfo('ret'));

        $client->retryUsing(new class() extends GenericRetryStrategy
        {
            public function __construct(
                array $statusCodes = self::DEFAULT_RETRY_STATUS_CODES,
                int $delayMs = 1000,
                float $multiplier = 2.0,
                int $maxDelayMs = 0,
                float $jitter = 0.1
            ) {
                parent::__construct($statusCodes, 10, $multiplier, $maxDelayMs, $jitter);
            }
        }, maxRetries: 2);

        $this->expectException(ServerException::class);
        $client->request('GET', 'http://foo.com');
    }
}

class DummyClientForRetryableClientTest implements HttpClientInterface
{
    use DecoratorTrait;
    use RetryableClient;

    public function __construct($response = null)
    {
        $this->client = new MockHttpClient($response);
    }

    public function getClient(): HttpClientInterface
    {
        return $this->client;
    }

    public function __call(string $name, array $arguments)
    {
        return $this->client->{$name}(...$arguments);
    }
}
