<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Payment\Order;

use EasyWeChat\Kernel\Support;
use EasyWeChat\Payment\Application;
use EasyWeChat\Payment\Order\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    protected function app()
    {
        return new Application([
            'app_id' => 'wx123456',
            'mch_id' => 'foo-merchant-id',
            'notify_url' => 'http://easywechat.org/notify',
        ]);
    }

    public function testUnify()
    {
        $client = $this->mockApiClient(Client::class, ['request'], $this->app());

        $order = [
            'trade_type' => 'NATIVE',
        ];

        // spbill_create_ip is null and trade_type === NATIVE
        $client->expects()->request('pay/unifiedorder', array_merge($order, [
            'spbill_create_ip' => Support\get_server_ip(),
            'appid' => 'wx123456',
            'notify_url' => 'http://easywechat.org/notify',
        ]))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->unify($order));

        // spbill_create_ip is null and trade_type !== Order::NATIVE
        $order = [
            'trade_type' => 'JSAPI',
        ];
        $client->expects()->request('pay/unifiedorder', array_merge($order, [
            'spbill_create_ip' => Support\get_client_ip(),
            'appid' => 'wx123456',
            'notify_url' => 'http://easywechat.org/notify',
            ]))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->unify($order));

        // spbill_create_ip is not null.
        $order = [
            'trade_type' => 'JSAPI',
            'spbill_create_ip' => '192.168.0.1',
        ];
        $client->expects()->request('pay/unifiedorder', array_merge($order, [
            'appid' => 'wx123456',
            'notify_url' => 'http://easywechat.org/notify',
        ]))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->unify($order));

        // set notify-url when unify order.
        $order = [
            'trade_type' => 'JSAPI',
            'notify_url' => 'http://foobar.baz/notify',
        ];
        $client->expects()->request('pay/unifiedorder', array_merge($order, [
            'spbill_create_ip' => Support\get_client_ip(),
            'appid' => 'wx123456',
            'notify_url' => 'http://foobar.baz/notify',
        ]))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->unify($order));
    }

    public function testQueryByOutTradeNumber()
    {
        $client = $this->mockApiClient(Client::class, ['request'], $this->app());
        $client->expects()->request('pay/orderquery', [
            'appid' => 'wx123456',
            'out_trade_no' => 'out-trade-no-123',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->queryByOutTradeNumber('out-trade-no-123'));
    }

    public function testQueryByTransactionId()
    {
        $client = $this->mockApiClient(Client::class, ['request'], $this->app());
        $client->expects()->request('pay/orderquery', [
            'appid' => 'wx123456',
            'transaction_id' => 'transaction-id-123',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->queryByTransactionId('transaction-id-123'));
    }

    public function testClose()
    {
        $client = $this->mockApiClient(Client::class, ['request'], $this->app());
        $client->expects()->request('pay/closeorder', [
            'appid' => 'wx123456',
            'out_trade_no' => 'out-no-123',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->close('out-no-123'));
    }
}
