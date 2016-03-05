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
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace Overtrue\Wechat;

/**
 * 数据统计
 */
class Stats
{
    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

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
    const  API_UPSTREAM_MESSSAGE_SUMMARY = 'https://api.weixin.qq.com/datacube/getupstreammsg';
    // 获取消息分送分时数据
    const  API_UPSTREAM_MESSSAGE_HOURLY = 'https://api.weixin.qq.com/datacube/getupstreammsghour';
    // 获取消息发送周数据
    const  API_UPSTREAM_MESSSAGE_WEEKLY = 'https://api.weixin.qq.com/datacube/getupstreammsgweek';
    // 获取消息发送月数据
    const  API_UPSTREAM_MESSSAGE_MONTHLY = 'https://api.weixin.qq.com/datacube/getupstreammsgmonth';
    // 获取消息发送分布数据
    const  API_UPSTREAM_MESSSAGE_DIST_SUMMARY = 'https://api.weixin.qq.com/datacube/getupstreammsgdist';
    // 获取消息发送分布周数据
    const  API_UPSTREAM_MESSSAGE_DIST_WEEKLY = 'https://api.weixin.qq.com/datacube/getupstreammsgdistweek';
    // 获取消息发送分布月数据
    const  API_UPSTREAM_MESSSAGE_DIST_MONTHLY = 'https://api.weixin.qq.com/datacube/getupstreammsgdistmonth?';
    // 获取接口分析数据
    const  API_INTERFACE_SUMMARY = 'https://api.weixin.qq.com/datacube/getinterfacesummary';
    // 获取接口分析分时数据
    const  API_INTERFACE_SUMMARY_HOURLY = 'https://api.weixin.qq.com/datacube/getinterfacesummaryhour';

    /**
     * constructor.
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->http = new Http(new AccessToken($appId, $appSecret));
    }

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
    public function upstreamMesssageSummary($from, $to)
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
    public function upstreamMesssageHourly($from, $to)
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
    public function upstreamMesssageWeekly($from, $to)
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
    public function upstreamMesssageMonthly($from, $to)
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
    public function upstreamMesssageDistSummary($from, $to)
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
    public function upstreamMesssageDistWeekly($from, $to)
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
    public function upstreamMesssageDistMonthly($from, $to)
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
     * 查询数据.
     *
     * @param string $api
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    protected function query($api, $from, $to)
    {
        $params = array(
                   'begin_date' => $from,
                   'end_date' => $to,
                  );

        $result = $this->http->jsonPost($api, $params);

        return $result['list'];
    }
}
