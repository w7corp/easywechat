<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Card;

use EasyWeChat\OfficialAccount\Card\GiftCardOrderClient;
use EasyWeChat\Tests\TestCase;

class GiftCartOrderClientTest extends TestCase
{
    public function testGet()
    {
        $client = $this->mockApiClient(GiftCardOrderClient::class);

        $params = [
            'order_id' => 'mock-order-id',
        ];

        $client->expects()->httpPostJson('card/giftcard/order/get', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->get('mock-order-id'));
    }

    public function testList()
    {
        $client = $this->mockApiClient(GiftCardOrderClient::class);

        $client->expects()->httpPostJson('card/giftcard/order/batchget', ['begin_time' => 1472400000, 'end_time' => 1472716604, 'offset' => 0, 'count' => 2, 'sort_type' => 'ASC'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->list(1472400000, 1472716604, 0, 2, 'ASC'));

        $client->expects()->httpPostJson('card/giftcard/order/batchget', ['begin_time' => 1472400000, 'end_time' => 1472716604, 'offset' => 0, 'count' => 2, 'sort_type' => 'DESC'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->list(1472400000, 1472716604, 0, 2, 'DESC'));
    }

    public function testRefund()
    {
        $client = $this->mockApiClient(GiftCardOrderClient::class);

        $params = [
            'order_id' => 'mock-order-id',
        ];

        $client->expects()->httpPostJson('card/giftcard/order/refund', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->refund('mock-order-id'));
    }
}
