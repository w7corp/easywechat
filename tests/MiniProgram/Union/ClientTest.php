<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MiniProgram\Union;

use EasyWeChat\MiniProgram\Union\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testCreatePromotion()
    {
        $client = $this->mockApiClient(Client::class);
        $params = [
            'promotionSourceName' => 'foo'
        ];

        $client->expects()->httpPostJson('union/promoter/promotion/add', $params)->andReturn('mock-result');
        $response = $client->createPromotion('foo');

        $this->assertSame('mock-result', $response);
    }

    public function testDeletePromotion()
    {
        $client = $this->mockApiClient(Client::class);
        $params = [
            'promotionSourceName' => 'foo',
            'promotionSourcePid' => 1,
        ];

        $client->expects()->httpPostJson('union/promoter/promotion/del', $params)->andReturn('mock-result');
        $response = $client->deletePromotion(1, 'foo');

        $this->assertSame('mock-result', $response);
    }

    public function testUpdatePromotion()
    {
        $client = $this->mockApiClient(Client::class);
        $params = [
            'previousPromotionInfo' => [],
            'promotionInfo' => [],
        ];

        $client->expects()->httpPostJson('union/promoter/promotion/upd', $params)->andReturn('mock-result');
        $response = $client->updatePromotion([], []);

        $this->assertSame('mock-result', $response);
    }

    public function testGetPromotionSourceList()
    {
        $client = $this->mockApiClient(Client::class);
        $params = [
            'start' => 0,
            'limit' => 20
        ];

        $client->expects()->httpGet('union/promoter/promotion/list', $params)->andReturn('mock-result');
        $response = $client->getPromotionSourceList(0, 20);

        $this->assertSame('mock-result', $response);
    }

    public function testGetProductCategory()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpGet('union/promoter/product/category')->andReturn('mock-result');
        $response = $client->getProductCategory();

        $this->assertSame('mock-result', $response);
    }

    public function testGetProductList()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpGet('union/promoter/product/list', [])->andReturn('mock-result');
        $response = $client->getProductList([]);

        $this->assertSame('mock-result', $response);
    }

    public function testGetProductMaterial()
    {
        $client = $this->mockApiClient(Client::class);
        $params = [
            'pid' => 0,
            'productList' => [],
        ];

        $client->expects()->httpPostJson('union/promoter/product/generate', $params)->andReturn('mock-result');
        $response = $client->getProductMaterial(0, []);

        $this->assertSame('mock-result', $response);
    }

    public function testGetOrderInfo()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('union/promoter/order/info', [])->andReturn('mock-result');
        $response = $client->getOrderInfo([]);

        $this->assertSame('mock-result', $response);
    }

    public function testSearchOrder()
    {
        $client = $this->mockApiClient(Client::class);
        $params = [
            'page' => 1,
            'startTimestamp' => '',
            'endTimestamp' => '',
            'commissionStatus' => ''
        ];

        $client->expects()->httpGet('union/promoter/order/search', $params)->andReturn('mock-result');
        $response = $client->searchOrder();

        $this->assertSame('mock-result', $response);
    }

    public function testGetFeaturedProducts()
    {
        $client = $this->mockApiClient(Client::class);
        $params = [
            'from' => 0,
            'limit' => 10,
            'minPrice' => 1
        ];

        $client->expects()->httpGet('union/promoter/product/select', $params)->andReturn('mock-result');
        $response = $client->getFeaturedProducts($params);

        $this->assertSame('mock-result', $response);
    }

    public function testGetTargetPlanInfo()
    {
        $client = $this->mockApiClient(Client::class);
        $params = [
            'planInvitationUrl' => 'https://foo.bar'
        ];
        $client->expects()->httpGet('union/promoter/target/plan_info', $params)->andReturn('mock-result');
        $response = $client->getTargetPlanInfo($params);

        $this->assertSame('mock-result', $response);
    }

    public function testApplyJoinTargetPlan()
    {
        $client = $this->mockApiClient(Client::class);
        $params = [
            'planId' => '123456',
            'applyReason' => 'foo'
        ];
        $client->expects()->httpPostJson('union/promoter/target/apply_target', $params)->andReturn('mock-result');
        $response = $client->applyJoinTargetPlan($params);

        $this->assertSame('mock-result', $response);
    }

    public function testGetTargetPlanStatus()
    {
        $client = $this->mockApiClient(Client::class);
        $params = [
            'planId' => '123456'
        ];
        $client->expects()->httpGet('union/promoter/target/apply_status', $params)->andReturn('mock-result');
        $response = $client->getTargetPlanStatus($params);

        $this->assertSame('mock-result', $response);
    }
}
