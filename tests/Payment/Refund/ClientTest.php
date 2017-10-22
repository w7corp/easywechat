<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Payment\Refund;

use EasyWeChat\Payment\Application;
use EasyWeChat\Payment\Refund\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testByOutTradeNumber()
    {
        $client = $this->mockApiClient(Client::class, ['safeRequest'], new Application())->makePartial();

        $orderNo = 'foo';
        $refundNo = 'bar';
        $totalFee = 1;
        $refundFee = 1;
        $optional = ['foo' => 'bar'];

        $params = array_merge([
            'out_trade_no' => $orderNo,
            'out_refund_no' => $refundNo,
            'total_fee' => $totalFee,
            'refund_fee' => $totalFee,
        ], $optional);

        $client->expects()->safeRequest('secapi/pay/refund', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->byOutTradeNumber($orderNo, $refundNo, $totalFee, $refundFee, $optional));
    }

    public function testByTransactionId()
    {
        $client = $this->mockApiClient(Client::class, ['safeRequest'], new Application())->makePartial();

        $orderNo = 'foo';
        $refundNo = 'bar';
        $totalFee = 1;
        $refundFee = 1;
        $optional = ['foo' => 'bar'];

        $params = array_merge([
            'transaction_id' => $orderNo,
            'out_refund_no' => $refundNo,
            'total_fee' => $totalFee,
            'refund_fee' => $totalFee,
        ], $optional);

        $client->expects()->safeRequest('secapi/pay/refund', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->byTransactionId($orderNo, $refundNo, $totalFee, $refundFee, $optional));
    }

    public function testQueryByTransactionId()
    {
        $client = $this->mockApiClient(Client::class, ['request'], new Application())->makePartial();

        $client->expects()->request('pay/refundquery', [
            'transaction_id' => 'foobar',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->queryByTransactionId('foobar'));
    }

    public function testQueryByOutTradeNumber()
    {
        $client = $this->mockApiClient(Client::class, ['request'], new Application())->makePartial();

        $client->expects()->request('pay/refundquery', [
            'out_trade_no' => 'foobar',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->queryByOutTradeNumber('foobar'));
    }

    public function testQueryByOutRefundNumber()
    {
        $client = $this->mockApiClient(Client::class, ['request'], new Application())->makePartial();

        $client->expects()->request('pay/refundquery', [
            'out_refund_no' => 'foobar',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->queryByOutRefundNumber('foobar'));
    }

    public function testQueryByRefundId()
    {
        $client = $this->mockApiClient(Client::class, ['request'], new Application())->makePartial();

        $client->expects()->request('pay/refundquery', [
            'refund_id' => 'foobar',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->queryByRefundId('foobar'));
    }
}
