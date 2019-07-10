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

use EasyWeChat\OfficialAccount\Card\CoinClient;
use EasyWeChat\Tests\TestCase;

class CoinClientTest extends TestCase
{
    public function testActivate()
    {
        $client = $this->mockApiClient(CoinClient::class);

        $client->expects()->httpGet('card/pay/activate')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->activate());
    }

    public function testSummary()
    {
        $client = $this->mockApiClient(CoinClient::class);

        $client->expects()->httpGet('card/pay/getcoinsinfo')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->summary());
    }

    public function testGetPrice()
    {
        $client = $this->mockApiClient(CoinClient::class);

        $cardId = 'mock-card-id';
        $quantity = 100;
        $params = [
            'card_id' => $cardId,
            'quantity' => $quantity,
        ];
        $client->expects()->httpPostJson('card/pay/getpayprice', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getPrice($cardId, $quantity));
    }

    public function testRecharge()
    {
        $client = $this->mockApiClient(CoinClient::class);

        $client->expects()->httpPostJson('card/pay/recharge', [
            'coin_count' => 100,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->recharge(100));
    }

    public function testOrder()
    {
        $client = $this->mockApiClient(CoinClient::class);

        $client->expects()->httpPostJson('card/pay/getorder', [
            'order_id' => 'mock-order-id',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->order('mock-order-id'));
    }

    public function testOrders()
    {
        $client = $this->mockApiClient(CoinClient::class);

        $client->expects()->httpPostJson('card/pay/getorderlist', ['foo' => 'bar'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->orders(['foo' => 'bar']));
    }

    public function testConfirm()
    {
        $client = $this->mockApiClient(CoinClient::class);

        $client->expects()->httpPostJson('card/pay/confirm', [
            'card_id' => 'mock-card-id',
            'order_id' => 'mock-order-id',
            'quantity' => 20,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->confirm('mock-card-id', 'mock-order-id', 20));
    }
}
