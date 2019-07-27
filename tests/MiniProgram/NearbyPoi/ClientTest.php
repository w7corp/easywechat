<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MiniProgram\NearbyPoi;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\MiniProgram\NearbyPoi\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testAdd()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('wxa/addnearbypoi', [
            'is_comm_nearby' => '1',
            'poi_id' => '',
            'foo' => 'bar',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->add(['foo' => 'bar']));
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('wxa/addnearbypoi', [
            'is_comm_nearby' => '1',
            'poi_id' => 'mock-poi-id',
            'foo' => 'bar',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->update('mock-poi-id', ['foo' => 'bar']));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('wxa/delnearbypoi', ['poi_id' => 'mock-poi-id'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->delete('mock-poi-id'));
    }

    public function testList()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpGet('wxa/getnearbypoilist', ['page' => 5, 'page_rows' => 10])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->list(5, 10));
    }

    public function testWithInvalidStatus()
    {
        $client = $this->mockApiClient(Client::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('status should be 0 or 1.');
        $client->setVisibility('mock-poi-id', 2);
    }

    public function testSetVisibility()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('wxa/setnearbypoishowstatus', [
            'poi_id' => 'mock-poi-id',
            'status' => 0,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->setVisibility('mock-poi-id', 0));
    }
}
