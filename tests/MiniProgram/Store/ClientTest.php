<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MiniProgram\Store;

use EasyWeChat\MiniProgram\Store\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testCategories()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpGet('wxa/get_merchant_category')->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->categories());
    }

    public function testDistrict()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpGet('wxa/get_district')->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->districts());
    }

    public function testSearchFromMap()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('wxa/search_map_poi', [
            'districtid' => 2,
            'keyword' => '北京',
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->searchFromMap(2, '北京'));
    }

    public function testCreateMerchant()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('wxa/apply_merchant', [
            'foo' => 'bar',
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->createMerchant(['foo' => 'bar']));
    }

    public function testUpdateMerchant()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('wxa/modify_merchant', [
            'foo' => 'bar',
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->updateMerchant(['foo' => 'bar']));
    }

    public function testCreateFromMap()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('wxa/create_map_poi', [
            'foo' => 'bar',
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->createFromMap(['foo' => 'bar']));
    }

    public function testCreate()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('wxa/add_store', [
            'foo' => 'bar',
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->create(['foo' => 'bar']));
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('wxa/update_store', [
            'foo' => 'bar',
            'poi_id' => 246,
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->update(246, ['foo' => 'bar']));
    }

    public function testGet()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('wxa/get_store_info', ['poi_id' => 44])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->get(44));
    }

    public function testList()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('wxa/get_store_list', [
            'offset' => 0,
            'limit' => 10,
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->list());

        $client->expects()->httpPostJson('wxa/get_store_list', [
            'offset' => 1,
            'limit' => 20,
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->list(1, 20));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('wxa/del_store', ['poi_id' => 12])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->delete(12));
    }
}
