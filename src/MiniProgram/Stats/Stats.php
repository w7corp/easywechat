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
 * Stats.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @copyright 2017
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\MiniProgram\Stats;

use EasyWeChat\MiniProgram\Core\AbstractMiniProgram;

class Stats extends AbstractMiniProgram
{
    const SUMMARY_TREND = 'https://api.weixin.qq.com/datacube/getweanalysisappiddailysummarytrend';
    const DAILY_VISIT_TREND = 'https://api.weixin.qq.com/datacube/getweanalysisappiddailyvisittrend';
    const WEEKLY_VISIT_TREND = 'https://api.weixin.qq.com/datacube/getweanalysisappidweeklyvisittrend';
    const MONTHLY_VISIT_TREND = 'https://api.weixin.qq.com/datacube/getweanalysisappidmonthlyvisittrend';
    const VISIT_DISTRIBUTION = 'https://api.weixin.qq.com/datacube/getweanalysisappidvisitdistribution';
    const DAILY_RETAIN_INFO = 'https://api.weixin.qq.com/datacube/getweanalysisappiddailyretaininfo';
    const WEEKLY_RETAIN_INFO = 'https://api.weixin.qq.com/datacube/getweanalysisappidweeklyretaininfo';
    const MONTHLY_RETAIN_INFO = 'https://api.weixin.qq.com/datacube/getweanalysisappidmonthlyretaininfo';
    const VISIT_PAGE = 'https://api.weixin.qq.com/datacube/getweanalysisappidvisitpage';

    /**
     * Get summary trend.
     *
     * @param string $from
     * @param string $to
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function summaryTrend($from, $to)
    {
        return $this->query(self::SUMMARY_TREND, $from, $to);
    }

    /**
     * Get daily visit trend.
     *
     * @param string $from
     * @param string $to
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function dailyVisitTrend($from, $to)
    {
        return $this->query(self::DAILY_VISIT_TREND, $from, $to);
    }

    /**
     * Get weekly visit trend.
     *
     * @param string $from
     * @param string $to
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function weeklyVisitTrend($from, $to)
    {
        return $this->query(self::WEEKLY_VISIT_TREND, $from, $to);
    }

    /**
     * Get monthly visit trend.
     *
     * @param string $from
     * @param string $to
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function monthlyVisitTrend($from, $to)
    {
        return $this->query(self::MONTHLY_VISIT_TREND, $from, $to);
    }

    /**
     * Get visit distribution.
     *
     * @param string $from
     * @param string $to
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function visitDistribution($from, $to)
    {
        return $this->query(self::VISIT_DISTRIBUTION, $from, $to);
    }

    /**
     * Get daily retain info.
     *
     * @param string $from
     * @param string $to
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function dailyRetainInfo($from, $to)
    {
        return $this->query(self::DAILY_RETAIN_INFO, $from, $to);
    }

    /**
     * Get weekly retain info.
     *
     * @param string $from
     * @param string $to
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function weeklyRetainInfo($from, $to)
    {
        return $this->query(self::WEEKLY_RETAIN_INFO, $from, $to);
    }

    /**
     * Get monthly retain info.
     *
     * @param string $from
     * @param string $to
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function montylyRetainInfo($from, $to)
    {
        return $this->query(self::MONTHLY_RETAIN_INFO, $from, $to);
    }

    /**
     * Get visit page.
     *
     * @param string $from
     * @param string $to
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function visitPage($from, $to)
    {
        return $this->query(self::VISIT_PAGE, $from, $to);
    }

    /**
     * Unify query.
     *
     * @param string $from
     * @param string $to
     *
     * @return \EasyWeChat\Support\Collection
     */
    protected function query($api, $from, $to)
    {
        $params = [
            'begin_date' => $from,
            'end_date' => $to,
        ];

        return $this->parseJSON('json', [$api, $params]);
    }
}
