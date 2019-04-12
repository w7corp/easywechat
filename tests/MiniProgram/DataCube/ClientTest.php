<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MiniProgram\DataCube;

use EasyWeChat\MiniProgram\DataCube\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testSummaryTrend()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getweanalysisappiddailysummarytrend', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->summaryTrend('2017-08-02', '2017-08-10'));
    }

    public function testDailyVisitTrend()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getweanalysisappiddailyvisittrend', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->dailyVisitTrend('2017-08-02', '2017-08-10'));
    }

    public function testWeeklyVisitTrend()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getweanalysisappidweeklyvisittrend', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->weeklyVisitTrend('2017-08-02', '2017-08-10'));
    }

    public function testMonthlyVisitTrend()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getweanalysisappidmonthlyvisittrend', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->monthlyVisitTrend('2017-08-02', '2017-08-10'));
    }

    public function testVisitDistribution()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getweanalysisappidvisitdistribution', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->visitDistribution('2017-08-02', '2017-08-10'));
    }

    public function testDailyRetainInfo()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getweanalysisappiddailyretaininfo', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->dailyRetainInfo('2017-08-02', '2017-08-10'));
    }

    public function testWeeklyRetainInfo()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getweanalysisappidweeklyretaininfo', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->weeklyRetainInfo('2017-08-02', '2017-08-10'));
    }

    public function testMonthlyRetainInfo()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getweanalysisappidmonthlyretaininfo', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->monthlyRetainInfo('2017-08-02', '2017-08-10'));
    }

    public function testVisitPage()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getweanalysisappidvisitpage', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->visitPage('2017-08-02', '2017-08-10'));
    }

    public function testUserPortrait()
    {
        $client = $this->mockApiClient(Client::class, ['query']);

        $client->expects()->query('datacube/getweanalysisappiduserportrait', '2017-08-02', '2017-08-10')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->userPortrait('2017-08-02', '2017-08-10'));
    }

    public function testQuery()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        $client->expects()->httpPostJson('path/to/api', [
            'begin_date' => '2017-08-02',
            'end_date' => '2017-08-10',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->query('path/to/api', '2017-08-02', '2017-08-10'));
    }
}
