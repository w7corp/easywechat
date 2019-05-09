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

use EasyWeChat\OfficialAccount\ShakeAround\GroupClient;
use EasyWeChat\Tests\TestCase;

class GroupClientTest extends TestCase
{
    public function testCreate()
    {
        $client = $this->mockApiClient(GroupClient::class);

        $client->expects()->httpPostJson('shakearound/device/group/add', ['group_name' => 'foo'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->create('foo'));
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(GroupClient::class);

        $client->expects()->httpPostJson('shakearound/device/group/update', [
            'group_id' => 11,
            'group_name' => 'foo',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->update(11, 'foo'));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(GroupClient::class);

        $client->expects()->httpPostJson('shakearound/device/group/delete', [
            'group_id' => 11,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->delete(11));
    }

    public function testList()
    {
        $client = $this->mockApiClient(GroupClient::class);

        $client->expects()->httpPostJson('shakearound/device/group/getlist', [
            'begin' => 11,
            'count' => 50,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->list(11, 50));
    }

    public function testGet()
    {
        $client = $this->mockApiClient(GroupClient::class);

        $client->expects()->httpPostJson('shakearound/device/group/getdetail', [
            'group_id' => 66,
            'begin' => 11,
            'count' => 50,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->get(66, 11, 50));
    }

    public function testAddDevices()
    {
        $client = $this->mockApiClient(GroupClient::class);

        $client->expects()->httpPostJson('shakearound/device/group/adddevice', [
            'group_id' => 66,
            'device_identifiers' => [['device_id' => 10011]],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->addDevices(66, [['device_id' => 10011]]));
    }

    public function testRemoveDevices()
    {
        $client = $this->mockApiClient(GroupClient::class);

        $client->expects()->httpPostJson('shakearound/device/group/deletedevice', [
            'group_id' => 66,
            'device_identifiers' => [['device_id' => 10011]],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->removeDevices(66, [['device_id' => 10011]]));
    }
}
