<?php

namespace EasyWeChat\Tests\Kernel\Traits;

use EasyWeChat\Kernel\Client;
use EasyWeChat\Kernel\Traits\InteractWithClient;
use PHPUnit\Framework\TestCase;

class InteractWithClientTest extends TestCase
{
    public function test_get_and_set_client()
    {
        $app = new DummyClassForInteractWithClientTest();

        $this->assertInstanceOf(Client::class, $app->getClient());
        $this->assertSame($app->getClient(), $app->getClient());

        // set
        $client = new Client();
        $app->setClient($client);
        $this->assertSame($client, $app->getClient());
    }
}

class DummyClassForInteractWithClientTest
{
    use InteractWithClient;

    public function createClient(): Client
    {
        return new Client();
    }
}
