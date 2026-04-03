<?php

namespace EasyWeChat\Tests\Kernel\Traits;

use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use EasyWeChat\Kernel\Traits\InteractWithClient;
use EasyWeChat\Kernel\Traits\InteractWithHttpClient;
use EasyWeChat\Tests\TestCase;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class InteractWithHttpClientTest extends TestCase
{
    public function test_get_and_set_http_client()
    {
        $app = new DummyClassForInteractWithHttpClientTest;

        $this->assertInstanceOf(HttpClientInterface::class, $app->getHttpClient());
        $this->assertSame($app->getHttpClient(), $app->getHttpClient());

        // set
        $client = new CurlHttpClient;
        $app->setHttpClient($client);
        $this->assertSame($client, $app->getHttpClient());
    }

    public function test_set_http_client_refreshes_default_client()
    {
        $app = new DummyClassForInteractWithHttpClientTest;

        $firstResponse = new MockResponse('{}');
        $app->setHttpClient(new MockHttpClient($firstResponse, 'https://easywechat.com/'));

        $firstClient = $app->getClient();
        $firstClient->request('GET', 'foo');

        $secondResponse = new MockResponse('{}');
        $app->setHttpClient(new MockHttpClient($secondResponse, 'https://easywechat.com/'));

        $secondClient = $app->getClient();
        $secondClient->request('GET', 'foo');

        $this->assertNotSame($firstClient, $secondClient);
        $this->assertSame('https://easywechat.com/foo', $firstResponse->getRequestUrl());
        $this->assertSame('https://easywechat.com/foo', $secondResponse->getRequestUrl());
    }

    public function test_set_http_client_preserves_custom_client()
    {
        $app = new DummyClassForInteractWithHttpClientTest;

        $client = new AccessTokenAwareClient;
        $app->setClient($client);
        $app->setHttpClient(new CurlHttpClient);

        $this->assertSame($client, $app->getClient());
    }
}

class DummyClassForInteractWithHttpClientTest
{
    use InteractWithClient;
    use InteractWithHttpClient;

    public function createClient(): AccessTokenAwareClient
    {
        return new AccessTokenAwareClient($this->getHttpClient());
    }

    protected function afterHttpClientUpdated(): void
    {
        $this->resetClient();
    }
}
