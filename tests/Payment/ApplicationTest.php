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
use EasyWeChat\Payment\Client;
use EasyWeChat\Tests\TestCase;

class ApplicationTest extends TestCase
{
    public function testMagicCall()
    {
        $app = new Application([
            'app_id' => 'wx123456',
            'merchant_id' => 'foo-merchant-id',
        ]);

        $this->assertInstanceOf(\EasyWeChat\BasicService\Url\Client::class, $app->url);
        $this->assertInstanceOf(\EasyWeChat\OfficialAccount\Auth\AccessToken::class, $app->access_token);
        $this->assertInstanceOf(\EasyWeChat\Payment\Coupon\Client::class, $app->coupon);
        $this->assertInstanceOf(\EasyWeChat\Payment\Redpack\Client::class, $app->redpack);
        $this->assertInstanceOf(\EasyWeChat\Payment\Transfer\Client::class, $app->transfer);
        $this->assertInstanceOf(\EasyWeChat\Payment\Jssdk\Client::class, $app->jssdk);
        $this->assertInstanceOf(Client::class, $app->sandboxMode(true));

        // test calling nonexistent method
        $this->expectException(\PHPUnit\Framework\Error\Warning::class);
        $app->noncexistentMethod('foo');
    }
}
