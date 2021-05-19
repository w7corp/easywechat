<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Payment;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\Pay\Application;
use EasyWeChat\Tests\TestCase;

class ApplicationTest extends TestCase
{
    public function testMagicCall()
    {
        $app = new Application([
            'app_id' => 'wx123456',
            'mch_id' => 'foo-merchant-id',
        ]);

        $this->assertInstanceOf(\EasyWeChat\BasicService\Url\Client::class, $app->url);
        $this->assertInstanceOf(\EasyWeChat\OfficialAccount\Auth\AccessToken::class, $app->access_token);
        $this->assertInstanceOf(\EasyWeChat\Pay\Coupon\Client::class, $app->coupon);
        $this->assertInstanceOf(\EasyWeChat\Pay\Bill\Client::class, $app->bill);
        $this->assertInstanceOf(\EasyWeChat\Pay\Order\Client::class, $app->order);
        $this->assertInstanceOf(\EasyWeChat\Pay\Refund\Client::class, $app->refund);
        $this->assertInstanceOf(\EasyWeChat\Pay\Reverse\Client::class, $app->reverse);
        $this->assertInstanceOf(\EasyWeChat\Pay\Sandbox\Client::class, $app->sandbox);
        $this->assertInstanceOf(\EasyWeChat\Pay\Redpack\Client::class, $app->redpack);
        $this->assertInstanceOf(\EasyWeChat\Pay\Transfer\Client::class, $app->transfer);
        $this->assertInstanceOf(\EasyWeChat\Pay\Jssdk\Client::class, $app->jssdk);

        // test calling nonexistent method
        $this->expectException(\TypeError::class);
        $app->noncexistentMethod('foo');
    }

    public function testScheme()
    {
        $app = new Application([
            'app_id' => 'wx123456',
            'mch_id' => 'foo-merchant-id',
        ]);

        $this->assertStringStartsWith('weixin://wxpay/bizpayurl?appid=wx123456&mch_id=foo-merchant-id&time_stamp=', $app->scheme('product-id'));
    }

    public function testCodeUrlScheme()
    {
        $app = new Application([
            'app_id' => 'wx123456',
            'mch_id' => 'foo-merchant-id',
        ]);
        $this->assertStringStartsWith('weixin://wxpay/bizpayurl?sr=foo', $app->codeUrlScheme('foo'));
        $this->assertStringStartsWith('weixin://wxpay/bizpayurl?sr=foo/bar', $app->codeUrlScheme('foo/bar'));
    }

    public function testSetSubMerchant()
    {
        $app = new Application([
            'app_id' => 'wx123456',
            'mch_id' => 'foo-merchant-id',
        ]);
        $this->assertInstanceOf(Application::class, $app->setSubMerchant('sub-mchid', 'sub-appid'));

        $this->assertSame('sub-mchid', $app->config['sub_mch_id']);
        $this->assertSame('sub-appid', $app->config['sub_appid']);
    }

    public function testInSandbox()
    {
        $app = new Application([
            'sandbox' => true,
        ]);
        $this->assertTrue($app->inSandbox());

        $app = new Application([]);
        $this->assertFalse($app->inSandbox());
    }

    public function testGetKey()
    {
        $app = new Application(['key' => '88888888888888888888888888888888']);
        $this->assertSame('88888888888888888888888888888888', $app->getKey());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf("'%s' should be 32 chars length.", '1234'));
        $app = new Application(['key' => '1234']);
        $app->getKey();
    }

    public function testGetKeyInSandboxMode()
    {
        $app = new Application([
            'sandbox' => true,
            'key' => 'keyxxx',
        ]);
        $sandbox = \Mockery::mock(\EasyWeChat\Pay\Sandbox\Client::class.'[getKey]', new ServiceContainer());
        $sandbox->expects()->getKey()->andReturn('88888888888888888888888888888888');
        $app['sandbox'] = $sandbox;

        $this->assertSame('88888888888888888888888888888888', $app->getKey('foo'));
        $this->assertSame('keyxxx', $app->getKey('sandboxnew/pay/getsignkey'));
    }
}
