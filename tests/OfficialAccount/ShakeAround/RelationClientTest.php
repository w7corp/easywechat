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

use EasyWeChat\OfficialAccount\ShakeAround\RelationClient;
use EasyWeChat\Tests\TestCase;

class RelationClientTest extends TestCase
{
    public function testBindPages()
    {
        $client = $this->mockApiClient(RelationClient::class);

        $client->expects()->httpPostJson('shakearound/device/bindpage', [
            'device_identifier' => [
                ['device_id' => 10011],
                ['device_id' => 10012],
            ],
            'page_ids' => [1, 4],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->bindPages([
            ['device_id' => 10011],
            ['device_id' => 10012],
        ], [1, 4]));
    }

    public function testListByDeviceId()
    {
        $client = $this->mockApiClient(RelationClient::class);

        $client->expects()->httpPostJson('shakearound/relation/search', [
            'type' => 1,
            'device_identifier' => ['device_id' => 10011],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->listByDeviceId(['device_id' => 10011]));
    }

    public function testListByPageId()
    {
        $client = $this->mockApiClient(RelationClient::class);

        $client->expects()->httpPostJson('shakearound/relation/search', [
            'type' => 2,
            'page_id' => 6,
            'begin' => 5,
            'count' => 50,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->listByPageId(6, 5, 50));
    }
}
