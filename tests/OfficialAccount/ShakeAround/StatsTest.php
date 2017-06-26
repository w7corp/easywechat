<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * ShakeAroundStatsTest.php.
 *
 * @author    allen05ren <allen05ren@outlook.com>
 * @copyright 2016 overtrue <i@overtrue.me>
 *
 * @see       https://github.com/overtrue
 * @see       http://overtrue.me
 */

namespace EasyWeChat\Tests\OfficialAccount\ShakeAround;

use EasyWeChat\Applications\OfficialAccount\ShakeAround\StatsClient;
use EasyWeChat\Tests\TestCase;

class StatsTest extends TestCase
{
    public function getStats()
    {
        $stats = \Mockery::mock('EasyWeChat\Applications\OfficialAccount\ShakeAround\Stats[parseJSON]', [\Mockery::mock('EasyWeChat\Applications\OfficialAccount\Core\AccessToken')]);
        $stats->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return [
                'api' => $params[0],
                'params' => $params[1],
            ];
        });

        return $stats;
    }

    /**
     * Test deviceSummary().
     */
    public function testDeviceSummary()
    {
        $stats = $this->getStats();

        $expected = [
            'device_identifier' => [
                'device_id' => 10100,
                'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
                'major' => 10001,
                'minor' => 10002,
            ],
            'begin_date' => 1438704000,
            'end_date' => 1438704000,
        ];
        $result = $stats->deviceSummary([
            'device_id' => 10100,
            'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
            'major' => 10001,
            'minor' => 10002,
        ], 1438704000, 1438704000);

        $this->assertStringStartsWith(StatsClient::API_DEVICE, $result['api']);
        $this->assertSame($expected, $result['params']);
    }

    /**
     * Test batchDeviceSummary().
     */
    public function testBatchDeviceSummary()
    {
        $stats = $this->getStats();

        $expected = [
            'date' => 1438704000,
            'page_index' => 1,
        ];

        $result = $stats->batchDeviceSummary(1438704000, 1);

        $this->assertStringStartsWith(StatsClient::API_DEVICE_LIST, $result['api']);
        $this->assertSame($expected, $result['params']);
    }

    /**
     * Test pageSummary().
     */
    public function testPageSummary()
    {
        $stats = $this->getStats();

        $expected = [
            'page_id' => 1234,
            'begin_date' => 1438704000,
            'end_date' => 1438704000,
        ];

        $result = $stats->pageSummary(1234, 1438704000, 1438704000);

        $this->assertStringStartsWith(StatsClient::API_PAGE, $result['api']);
        $this->assertSame($expected, $result['params']);
    }

    /**
     * Test batchPageSummary().
     */
    public function testBatchPageSummary()
    {
        $stats = $this->getStats();

        $expected = [
            'date' => 1425139200,
            'page_index' => 1,
        ];

        $result = $stats->batchPageSummary(1425139200, 1);

        $this->assertStringStartsWith(StatsClient::API_PAGE_LIST, $result['api']);
        $this->assertSame($expected, $result['params']);
    }
}
