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

use EasyWeChat\OfficialAccount\ShakeAround\StatsClient;
use EasyWeChat\Tests\TestCase;

class StatsClientTest extends TestCase
{
    public function testDeviceSummary()
    {
        $client = $this->mockApiClient(StatsClient::class);

        $client->expects()->httpPostJson('shakearound/statistics/device', [
            'device_identifier' => ['device_id' => 10011],
            'begin_date' => 1438704000,
            'end_date' => 1438708000,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->deviceSummary(['device_id' => 10011], 1438704000, 1438708000));
    }

    public function testBatchDeviceSummary()
    {
        $client = $this->mockApiClient(StatsClient::class);

        $client->expects()->httpPostJson('shakearound/statistics/devicelist', [
            'date' => 1438704000,
            'page_index' => 5,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->devicesSummary(1438704000, 5));
    }

    public function testPageSummary()
    {
        $client = $this->mockApiClient(StatsClient::class);

        $client->expects()->httpPostJson('shakearound/statistics/page', [
            'page_id' => 10011,
            'begin_date' => 1438704000,
            'end_date' => 1438708000,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->pageSummary(10011, 1438704000, 1438708000));
    }

    public function testPagesSummary()
    {
        $client = $this->mockApiClient(StatsClient::class);

        $client->expects()->httpPostJson('shakearound/statistics/pagelist', [
            'date' => 1438704000,
            'page_index' => 5,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->pagesSummary(1438704000, 5));
    }
}
