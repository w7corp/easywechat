<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MiniProgram\Broadcast;

use EasyWeChat\MiniProgram\Broadcast\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testCreate()
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'coverImgUrl' => 'foo',
            'name' => 'bar',
            'priceType' => 1,
            'price' => 1.0,
            'price2' => 2.0,
            'url' => 'pages/goods/index.html?id=10',
        ];
        $client->expects()->httpPostJson('wxaapi/broadcast/goods/add', ['goodsInfo' => $params])->andReturn('mock-result');
        $response = $client->create($params);

        $this->assertSame('mock-result', $response);
    }

    public function testResetAudit()
    {
        $params = [
            'auditId' => '123456',
            'goodsId' => 1,
        ];
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('wxaapi/broadcast/goods/resetaudit', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->resetAudit('123456', 1));
    }

    public function testResubmitAudit()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('wxaapi/broadcast/goods/audit', ['goodsId' => 1])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->resubmitAudit(1));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('wxaapi/broadcast/goods/delete', ['goodsId' => 1])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->delete(1));
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'coverImgUrl' => 'foo',
            'name' => 'bar',
            'priceType' => 1,
            'price' => 1.0,
            'price2' => 2.0,
            'url' => 'pages/goods/index.html?id=10',
            'goodsId' => 1,
        ];
        $client->expects()->httpPostJson('wxaapi/broadcast/goods/update', ['goodsInfo' => $params])->andReturn('mock-result');
        $response = $client->update($params);

        $this->assertSame('mock-result', $response);
    }

    public function testGetGoodsWarehouse()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpGet('wxa/business/getgoodswarehouse', ['goods_ids' => [1]])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getGoodsWarehouse([1]));
    }

    public function testGetApproved()
    {
        $client = $this->mockApiClient(Client::class);
        $params = ['offset' => 1, 'limit' => 30, 'status' => 1];
        $client->expects()->httpGet('wxaapi/broadcast/goods/getapproved', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getApproved($params));
    }

    public function testAddGoods()
    {
        $client = $this->mockApiClient(Client::class);
        $params = ['ids' => [9, 11], 'roomId' => 223];
        $client->expects()->httpPost('wxaapi/broadcast/room/addgoods', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->addGoods($params));
    }
}
