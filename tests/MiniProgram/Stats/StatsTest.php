<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\tests\MiniProgram\Stats;

use EasyWeChat\Tests\TestCase;
use Mockery as m;

class StatsTest extends TestCase
{
    public function getStats()
    {
        $stats = m::mock('EasyWeChat\Applications\MiniProgram\Stats\Client[parseJSON]', [m::mock('EasyWeChat\Applications\MiniProgram\AccessToken'), []]);
        $stats->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return [
                'api' => $params[0],
                'params' => empty($params[1]) ? null : $params[1],
            ];
        });

        return $stats;
    }

    /**
     * Test summaryTrend().
     */
    public function testSummaryTrend()
    {
        $stats = $this->getStats();
        $result = $stats->summaryTrend('20170313', '20170313');

        $this->assertEquals('https://api.weixin.qq.com/datacube/getweanalysisappiddailysummarytrend', $result['api']);
        $this->assertEquals(['begin_date' => '20170313', 'end_date' => '20170313'], $result['params']);
    }

    /**
     * Test dailyVisitTrend().
     */
    public function testDailyVisitTrend()
    {
        $stats = $this->getStats();
        $result = $stats->dailyVisitTrend('20170313', '20170313');

        $this->assertEquals('https://api.weixin.qq.com/datacube/getweanalysisappiddailyvisittrend', $result['api']);
        $this->assertEquals(['begin_date' => '20170313', 'end_date' => '20170313'], $result['params']);
    }

    /**
     * Test weeklyVisitTrend().
     */
    public function testWeeklyVisitTrend()
    {
        $stats = $this->getStats();
        $result = $stats->weeklyVisitTrend('20170306', '20170312');

        $this->assertEquals('https://api.weixin.qq.com/datacube/getweanalysisappidweeklyvisittrend', $result['api']);
        $this->assertEquals(['begin_date' => '20170306', 'end_date' => '20170312'], $result['params']);
    }

    /**
     * Test monthlyVisitTrend().
     */
    public function testMonthlyVisitTrend()
    {
        $stats = $this->getStats();
        $result = $stats->monthlyVisitTrend('20170201', '20170228');

        $this->assertEquals('https://api.weixin.qq.com/datacube/getweanalysisappidmonthlyvisittrend', $result['api']);
        $this->assertEquals(['begin_date' => '20170201', 'end_date' => '20170228'], $result['params']);
    }

    /**
     * Test visitDistribution().
     */
    public function testVisitDistribution()
    {
        $stats = $this->getStats();
        $result = $stats->visitDistribution('20170313', '20170313');

        $this->assertEquals('https://api.weixin.qq.com/datacube/getweanalysisappidvisitdistribution', $result['api']);
        $this->assertEquals(['begin_date' => '20170313', 'end_date' => '20170313'], $result['params']);
    }

    /**
     * Test dailyRetainInfo().
     */
    public function testDailyRetainInfo()
    {
        $stats = $this->getStats();
        $result = $stats->dailyRetainInfo('20170313', '20170313');

        $this->assertEquals('https://api.weixin.qq.com/datacube/getweanalysisappiddailyretaininfo', $result['api']);
        $this->assertEquals(['begin_date' => '20170313', 'end_date' => '20170313'], $result['params']);
    }

    /**
     * Test weeklyRetainInfo().
     */
    public function testWeeklyRetainInfo()
    {
        $stats = $this->getStats();
        $result = $stats->weeklyRetainInfo('20170306', '20170312');

        $this->assertEquals('https://api.weixin.qq.com/datacube/getweanalysisappidweeklyretaininfo', $result['api']);
        $this->assertEquals(['begin_date' => '20170306', 'end_date' => '20170312'], $result['params']);
    }

    /**
     * Test montylyRetainInfo().
     */
    public function testMontylyRetainInfo()
    {
        $stats = $this->getStats();
        $result = $stats->montylyRetainInfo('20170201', '20170228');

        $this->assertEquals('https://api.weixin.qq.com/datacube/getweanalysisappidmonthlyretaininfo', $result['api']);
        $this->assertEquals(['begin_date' => '20170201', 'end_date' => '20170228'], $result['params']);
    }

    /**
     * Test visitPage().
     */
    public function testVisitPage()
    {
        $stats = $this->getStats();
        $result = $stats->visitPage('20170313', '20170313');

        $this->assertEquals('https://api.weixin.qq.com/datacube/getweanalysisappidvisitpage', $result['api']);
        $this->assertEquals(['begin_date' => '20170313', 'end_date' => '20170313'], $result['params']);
    }
}
