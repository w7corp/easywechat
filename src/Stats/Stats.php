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
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Stats;

use EasyWeChat\Core\AbstractAPI;

/**
 * Class stats.
 */
class Stats extends AbstractAPI
{
    // 获取用户增减数据
    const  API_USER_SUMMARY = 'https://api.weixin.qq.com/datacube/getusersummary';
    // 获取累计用户数据
    const  API_USER_CUMULATE = 'https://api.weixin.qq.com/datacube/getusercumulate';
    // 获取图文群发每日数据
    const  API_ARTICLE_SUMMARY = 'https://api.weixin.qq.com/datacube/getarticlesummary';
    // 获取图文群发总数据
    const  API_ARTICLE_TOTAL = 'https://api.weixin.qq.com/datacube/getarticletotal';
    // 获取图文统计数据
    const  API_USER_READ_SUMMARY = 'https://api.weixin.qq.com/datacube/getuserread';
    // 获取图文统计分时数据
    const  API_USER_READ_HOURLY = 'https://api.weixin.qq.com/datacube/getuserreadhour';
    // 获取图文分享转发数据
    const  API_USER_SHARE_SUMMARY = 'https://api.weixin.qq.com/datacube/getusershare';
    // 获取图文分享转发分时数据
    const  API_USER_SHARE_HOURLY = 'https://api.weixin.qq.com/datacube/getusersharehour';
    // 获取消息发送概况数据
    const  API_UPSTREAM_MSG_SUMMARY = 'https://api.weixin.qq.com/datacube/getupstreammsg';
    // 获取消息分送分时数据
    const  API_UPSTREAM_MSG_HOURLY = 'https://api.weixin.qq.com/datacube/getupstreammsghour';
    // 获取消息发送周数据
    const  API_UPSTREAM_MSG_WEEKLY = 'https://api.weixin.qq.com/datacube/getupstreammsgweek';
    // 获取消息发送月数据
    const  API_UPSTREAM_MSG_MONTHLY = 'https://api.weixin.qq.com/datacube/getupstreammsgmonth';
    // 获取消息发送分布数据
    const  API_UPSTREAM_MSG_DIST_SUMMARY = 'https://api.weixin.qq.com/datacube/getupstreammsgdist';
    // 获取消息发送分布周数据
    const  API_UPSTREAM_MSG_DIST_WEEKLY = 'https://api.weixin.qq.com/datacube/getupstreammsgdistweek';
    // 获取消息发送分布月数据
    const  API_UPSTREAM_MSG_DIST_MONTHLY = 'https://api.weixin.qq.com/datacube/getupstreammsgdistmonth?';
    // 获取接口分析数据
    const  API_INTERFACE_SUMMARY = 'https://api.weixin.qq.com/datacube/getinterfacesummary';
    // 获取接口分析分时数据
    const  API_INTERFACE_SUMMARY_HOURLY = 'https://api.weixin.qq.com/datacube/getinterfacesummaryhour';
    // 拉取卡券概况数据接口
    const  API_CARD_SUMMARY = 'https://api.weixin.qq.com/datacube/getcardbizuininfo';
    // 获取免费券数据接口
    const  API_FREE_CARD_SUMMARY = 'https://api.weixin.qq.com/datacube/getcardcardinfo';
    // 拉取会员卡数据接口
    const  API_MEMBER_CARD_SUMMARY = 'https://api.weixin.qq.com/datacube/getcardmembercardinfo';

    /**
     * 获取用户增减数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function userSummary($from, $to)
    {
        return $this->query(self::API_USER_SUMMARY, $from, $to);
    }

    /**
     * 获取累计用户数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function userCumulate($from, $to)
    {
        return $this->query(self::API_USER_CUMULATE, $from, $to);
    }

    /**
     * 获取图文群发每日数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function articleSummary($from, $to)
    {
        return $this->query(self::API_ARTICLE_SUMMARY, $from, $to);
    }

    /**
     * 获取图文群发总数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function articleTotal($from, $to)
    {
        return $this->query(self::API_ARTICLE_TOTAL, $from, $to);
    }

    /**
     * 获取图文统计数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function userReadSummary($from, $to)
    {
        return $this->query(self::API_USER_READ_SUMMARY, $from, $to);
    }

    /**
     * 获取图文统计分时数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function userReadHourly($from, $to)
    {
        return $this->query(self::API_USER_READ_HOURLY, $from, $to);
    }

    /**
     * 获取图文分享转发数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function userShareSummary($from, $to)
    {
        return $this->query(self::API_USER_SHARE_SUMMARY, $from, $to);
    }

    /**
     * 获取图文分享转发分时数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function userShareHourly($from, $to)
    {
        return $this->query(self::API_USER_SHARE_HOURLY, $from, $to);
    }

    /**
     * 获取消息发送概况数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function upstreamMessageSummary($from, $to)
    {
        return $this->query(self::API_UPSTREAM_MSG_SUMMARY, $from, $to);
    }

    /**
     * 获取消息分送分时数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function upstreamMessageHourly($from, $to)
    {
        return $this->query(self::API_UPSTREAM_MSG_HOURLY, $from, $to);
    }

    /**
     * 获取消息发送周数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function upstreamMessageWeekly($from, $to)
    {
        return $this->query(self::API_UPSTREAM_MSG_WEEKLY, $from, $to);
    }

    /**
     * 获取消息发送月数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function upstreamMessageMonthly($from, $to)
    {
        return $this->query(self::API_UPSTREAM_MSG_MONTHLY, $from, $to);
    }

    /**
     * 获取消息发送分布数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function upstreamMessageDistSummary($from, $to)
    {
        return $this->query(self::API_UPSTREAM_MSG_DIST_SUMMARY, $from, $to);
    }

    /**
     * 获取消息发送分布周数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function upstreamMessageDistWeekly($from, $to)
    {
        return $this->query(self::API_UPSTREAM_MSG_DIST_WEEKLY, $from, $to);
    }

    /**
     * 获取消息发送分布月数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function upstreamMessageDistMonthly($from, $to)
    {
        return $this->query(self::API_UPSTREAM_MSG_DIST_MONTHLY, $from, $to);
    }

    /**
     * 获取接口分析数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function interfaceSummary($from, $to)
    {
        return $this->query(self::API_INTERFACE_SUMMARY, $from, $to);
    }

    /**
     * 获取接口分析分时数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function interfaceSummaryHourly($from, $to)
    {
        return $this->query(self::API_INTERFACE_SUMMARY_HOURLY, $from, $to);
    }

    /**
     * 拉取卡券概况数据接口.
     *
     * @param string $from
     * @param string $to
     * @param int    $condSource
     *
     * @return array
     */
    public function cardSummary($from, $to, $condSource = 0)
    {
        $ext = [
            'cond_source' => intval($condSource),
        ];

        return $this->query(self::API_CARD_SUMMARY, $from, $to, $ext);
    }

    /**
     * 获取免费券数据接口.
     *
     * @param string $from
     * @param string $to
     * @param int    $condSource
     * @param string $cardId
     *
     * @return array
     */
    public function freeCardSummary($from, $to, $condSource = 0, $cardId = '')
    {
        $ext = [
            'cond_source' => intval($condSource),
            'card_id' => $cardId,
        ];

        return $this->query(self::API_FREE_CARD_SUMMARY, $from, $to, $ext);
    }

    /**
     * 拉取会员卡数据接口.
     *
     * @param string $from
     * @param string $to
     * @param int    $condSource
     *
     * @return array
     */
    public function memberCardSummary($from, $to, $condSource = 0)
    {
        $ext = [
            'cond_source' => intval($condSource),
        ];

        return $this->query(self::API_MEMBER_CARD_SUMMARY, $from, $to, $ext);
    }

    /**
     * 查询数据.
     *
     * @param string $api
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    protected function query($api, $from, $to, array $ext = [])
    {
        $params = [
            'begin_date' => $from,
            'end_date' => $to,
        ];

        if (!empty($ext)) {
            $params = array_merge($params, $ext);
        }

        return $this->parseJSON('json', [$api, $params]);
    }
}
