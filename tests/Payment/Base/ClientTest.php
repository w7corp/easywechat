<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Payment\Base;

use EasyWeChat\Payment\Application;
use EasyWeChat\Payment\Base\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testPay()
    {
        $app = new Application();

        $client = $this->mockApiClient(Client::class, ['pay'], $app)->makePartial();

        $order = [
            'foo' => 'bar',
        ];

        $client->expects()->request('pay/micropay', $order)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->pay($order));
    }

    public function testAuthCodeToOpenId()
    {
        $app = new Application();

        $client = $this->mockApiClient(Client::class, 'authCodeToOpenId', $app)->makePartial();

        $client->expects()->request('tools/authcodetoopenid', ['auth_code' => 'foo'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->authCodeToOpenId('foo'));
    }
}
