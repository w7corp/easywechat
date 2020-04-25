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
    public function testAdd()
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'goodsInfo' => [
                'coverImgUrl' => 'foo',
                'name' => 'bar',
                'priceType' => 1,
                'price' => 1.0,
                'price2' => 2.0,
                'url' => 'pages/goods/index.html?id=10'
            ]
        ];
        $client->expects()->httpPostJson('wxaapi/broadcast/goods/add', $params)->andReturn('mock-result');
        $response = $client->add($params['goodsInfo']['coverImgUrl'], $params['goodsInfo']['name'], $params['goodsInfo']['priceType'], $params['goodsInfo']['price'], $params['goodsInfo']['price2'], $params['goodsInfo']['url']);
        
        $this->assertSame('mock-result', $response);
    }
    
    public function testResetAudit()
    {
        $params = [
            'auditId' => '123456',
            'goodsId' => 1
        ];
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('wxaapi/broadcast/goods/resetaudit', $params)->andReturn('mock-result');
        
        $this->assertSame('mock-result', $client->resetAudit('123456', 1));
    }
    
    public function testReSubmitAudit()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('wxaapi/broadcast/goods/audit', ['goodsId' => 1])->andReturn('mock-result');
        
        $this->assertSame('mock-result', $client->reSubmitAudit(1));
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
            'goodsInfo' => [
                'coverImgUrl' => 'foo',
                'name' => 'bar',
                'priceType' => 1,
                'price' => 1.0,
                'price2' => 2.0,
                'url' => 'pages/goods/index.html?id=10',
                'goodsId' => 1
            ]
        ];
        $client->expects()->httpPostJson('wxaapi/broadcast/goods/update', $params)->andReturn('mock-result');
        $response = $client->update($params['goodsInfo']['coverImgUrl'], $params['goodsInfo']['name'], $params['goodsInfo']['priceType'], $params['goodsInfo']['price'], $params['goodsInfo']['price2'], $params['goodsInfo']['url'], $params['goodsInfo']['goodsId']);
        
        $this->assertSame('mock-result', $response);
    }
    
    public function testGetGoodsWareHouse()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpGet('wxa/business/getgoodswarehouse', ['goods_ids' => [1]])->andReturn('mock-result');
        
        $this->assertSame('mock-result', $client->getGoodsWareHouse([1]));
    }
}
