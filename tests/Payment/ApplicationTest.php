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

use EasyWeChat\Payment\Application;
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
        $this->assertInstanceOf(\EasyWeChat\Payment\Coupon\Client::class, $app->coupon);
        $this->assertInstanceOf(\EasyWeChat\Payment\Bill\Client::class, $app->bill);
        $this->assertInstanceOf(\EasyWeChat\Payment\Order\Client::class, $app->order);
        $this->assertInstanceOf(\EasyWeChat\Payment\Refund\Client::class, $app->refund);
        $this->assertInstanceOf(\EasyWeChat\Payment\Reverse\Client::class, $app->reverse);
        $this->assertInstanceOf(\EasyWeChat\Payment\Sandbox\Client::class, $app->sandbox);
        $this->assertInstanceOf(\EasyWeChat\Payment\Redpack\Client::class, $app->redpack);
        $this->assertInstanceOf(\EasyWeChat\Payment\Transfer\Client::class, $app->transfer);
        $this->assertInstanceOf(\EasyWeChat\Payment\Jssdk\Client::class, $app->jssdk);

        // test calling nonexistent method
        $this->expectException(\PHPUnit\Framework\Error\Warning::class);
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
}
