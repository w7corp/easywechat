<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Payment\Reverse;

use EasyWeChat\Payment\Application;
use EasyWeChat\Payment\Reverse\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testByOutTradeNumber()
    {
        $client = $this->mockApiClient(Client::class, ['safeRequest'], new Application(['app_id' => 'wx123456']))->makePartial();

        $client->expects()->safeRequest('secapi/pay/reverse', [
            'appid' => 'wx123456',
            'out_trade_no' => 'foo',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->byOutTradeNumber('foo'));
    }

    public function testByTransactionId()
    {
        $client = $this->mockApiClient(Client::class, ['safeRequest'], new Application(['app_id' => 'wx123456']))->makePartial();

        $client->expects()->safeRequest('secapi/pay/reverse', ['appid' => 'wx123456', 'transaction_id' => 'foo'])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->byTransactionId('foo'));
    }
}
