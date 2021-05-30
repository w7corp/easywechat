<?php

namespace EasyWeChat\Tests\Pay;

use EasyWeChat\Kernel\ApiBuilder;
use EasyWeChat\Pay\Application;
use EasyWeChat\Pay\Client;
use EasyWeChat\Pay\Merchant;
use EasyWeChat\Tests\TestCase;

class ApplicationTest extends TestCase
{
    public function test_get_merchant()
    {
        $app = new Application([]);

        $this->assertInstanceOf(Merchant::class, $app->getMerchant());
        $this->assertSame($app->getMerchant(), $app->getMerchant());
    }

    public function test_get_client()
    {
        $app = new Application([]);

        $this->assertInstanceOf(Client::class, $app->getClient());
        $this->assertSame($app->getClient(), $app->getClient());
    }

    public function test_get_v3()
    {
        $app = new Application([]);

        $this->assertInstanceOf(ApiBuilder::class, $app->v3());
        $this->assertSame($app->v3(), $app->v3());

        $this->assertSame('/v3/', $app->v3()->getUri());
    }

    public function test_get_v2()
    {
        $app = new Application([]);

        $this->assertInstanceOf(ApiBuilder::class, $app->v2());
        $this->assertSame($app->v2(), $app->v2());

        $this->assertSame('/', $app->v2()->getUri());
    }
}
