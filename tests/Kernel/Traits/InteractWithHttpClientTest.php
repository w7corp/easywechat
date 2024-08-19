<?php

namespace EasyWeChat\Tests\Kernel\Traits;

use EasyWeChat\Kernel\Traits\InteractWithHttpClient;
use EasyWeChat\Tests\TestCase;
use Symfony\Component\HttpClient\CurlHttpClient;
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
}

class DummyClassForInteractWithHttpClientTest
{
    use InteractWithHttpClient;
}
