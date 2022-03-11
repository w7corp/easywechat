<?php

namespace EasyWeChat\Tests\Kernel\Traits;

use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use EasyWeChat\Kernel\Traits\InteractWithClient;
use EasyWeChat\Tests\TestCase;

class InteractWithClientTest extends TestCase
{
    public function test_get_and_set_client()
    {
        $app = new DummyClassForInteractWithClientTest();

        $this->assertInstanceOf(AccessTokenAwareClient::class, $app->getClient());
        $this->assertSame($app->getClient(), $app->getClient());

        // set
        $client = new AccessTokenAwareClient();
        $app->setClient($client);
        $this->assertSame($client, $app->getClient());
    }
}

class DummyClassForInteractWithClientTest
{
    use InteractWithClient;

    public function createClient(): AccessTokenAwareClient
    {
        return new AccessTokenAwareClient();
    }
}
