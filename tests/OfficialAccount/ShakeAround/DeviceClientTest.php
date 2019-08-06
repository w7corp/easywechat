<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\ShakeAround;

use EasyWeChat\OfficialAccount\ShakeAround\DeviceClient;
use EasyWeChat\Tests\TestCase;

class DeviceClientTest extends TestCase
{
    public function testApply()
    {
        $client = $this->mockApiClient(DeviceClient::class);

        $client->expects()->httpPostJson('shakearound/device/applyid', ['foo' => 'bar'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->apply(['foo' => 'bar']));
    }

    public function testStatus()
    {
        $client = $this->mockApiClient(DeviceClient::class);

        $client->expects()->httpPostJson('shakearound/device/applystatus', ['apply_id' => 77])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->status(77));
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(DeviceClient::class);

        $client->expects()->httpPostJson('shakearound/device/update', [
            'device_identifier' => [
                'device_id' => 10011,
            ],
            'comment' => 'mock-comment',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->update(['device_id' => 10011], 'mock-comment'));
    }

    public function testBindPoi()
    {
        $client = $this->mockApiClient(DeviceClient::class);

        $client->expects()->httpPostJson('shakearound/device/bindlocation', [
            'device_identifier' => [
                'device_id' => 10011,
            ],
            'poi_id' => 14,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->bindPoi(['device_id' => 10011], 14));
    }

    public function testBindThirdPoi()
    {
        $client = $this->mockApiClient(DeviceClient::class);

        $client->expects()->httpPostJson('shakearound/device/bindlocation', [
            'device_identifier' => [
                'device_id' => 10011,
            ],
            'poi_id' => 14,
            'type' => 2,
            'poi_appid' => 'mock-app-id',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->bindThirdPoi(['device_id' => 10011], 14, 'mock-app-id'));
    }

    public function testListByIds()
    {
        $client = $this->mockApiClient(DeviceClient::class);

        $client->expects()->httpPostJson('shakearound/device/search', [
            'device_identifiers' => [
                ['device_id' => 10011],
                ['device_id' => 10012],
            ],
            'type' => 1,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->listByIds([['device_id' => 10011], ['device_id' => 10012]]));
    }

    public function testList()
    {
        $client = $this->mockApiClient(DeviceClient::class);

        $client->expects()->httpPostJson('shakearound/device/search', [
            'type' => 2,
            'last_seen' => 45,
            'count' => 20,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->list(45, 20));
    }

    public function testListByApplyId()
    {
        $client = $this->mockApiClient(DeviceClient::class);

        $client->expects()->httpPostJson('shakearound/device/search', [
            'type' => 3,
            'apply_id' => 56,
            'last_seen' => 45,
            'count' => 20,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->listByApplyId(56, 45, 20));
    }

    public function testSearch()
    {
        $client = $this->mockApiClient(DeviceClient::class);

        $client->expects()->httpPostJson('shakearound/device/search', [
            'foo' => 'bar',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->search(['foo' => 'bar']));
    }
}
