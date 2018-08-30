<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\DataCube;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author overtrue <i@overtrue.me>
 */
class Client extends BaseClient
{
    /**
     * 获取用户增减数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return mixed
     */
    public function userSummary(string $from, string $to)
    {
        return $this->query('datacube/getusersummary', $from, $to);
    }

    /**
     * 获取累计用户数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return mixed
     */
    public function userCumulate(string $from, string $to)
    {
        return $this->query('datacube/getusercumulate', $from, $to);
    }

    /**
     * 获取图文群发每日数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return mixed
     */
    public function articleSummary(string $from, string $to)
    {
        return $this->query('datacube/getarticlesummary', $from, $to);
    }

    /**
     * 获取图文群发总数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return mixed
     */
    public function articleTotal(string $from, string $to)
    {
        return $this->query('datacube/getarticletotal', $from, $to);
    }

    /**
     * 获取图文统计数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return mixed
     */
    public function userReadSummary(string $from, string $to)
    {
        return $this->query('datacube/getuserread', $from, $to);
    }

    /**
     * 获取图文统计分时数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return mixed
     */
    public function userReadHourly(string $from, string $to)
    {
        return $this->query('datacube/getuserreadhour', $from, $to);
    }

    /**
     * 获取图文分享转发数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return mixed
     */
    public function userShareSummary(string $from, string $to)
    {
        return $this->query('datacube/getusershare', $from, $to);
    }

    /**
     * 获取图文分享转发分时数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return mixed
     */
    public function userShareHourly(string $from, string $to)
    {
        return $this->query('datacube/getusersharehour', $from, $to);
    }

    /**
     * 获取消息发送概况数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return mixed
     */
    public function upstreamMessageSummary(string $from, string $to)
    {
        return $this->query('datacube/getupstreammsg', $from, $to);
    }

    /**
     * 获取消息分送分时数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return mixed
     */
    public function upstreamMessageHourly(string $from, string $to)
    {
        return $this->query('datacube/getupstreammsghour', $from, $to);
    }

    /**
     * 获取消息发送周数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return mixed
     */
    public function upstreamMessageWeekly(string $from, string $to)
    {
        return $this->query('datacube/getupstreammsgweek', $from, $to);
    }

    /**
     * 获取消息发送月数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return mixed
     */
    public function upstreamMessageMonthly(string $from, string $to)
    {
        return $this->query('datacube/getupstreammsgmonth', $from, $to);
    }

    /**
     * 获取消息发送分布数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return mixed
     */
    public function upstreamMessageDistSummary(string $from, string $to)
    {
        return $this->query('datacube/getupstreammsgdist', $from, $to);
    }

    /**
     * 获取消息发送分布周数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return mixed
     */
    public function upstreamMessageDistWeekly(string $from, string $to)
    {
        return $this->query('datacube/getupstreammsgdistweek', $from, $to);
    }

    /**
     * 获取消息发送分布月数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return mixed
     */
    public function upstreamMessageDistMonthly(string $from, string $to)
    {
        return $this->query('datacube/getupstreammsgdistmonth', $from, $to);
    }

    /**
     * 获取接口分析数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return mixed
     */
    public function interfaceSummary(string $from, string $to)
    {
        return $this->query('datacube/getinterfacesummary', $from, $to);
    }

    /**
     * 获取接口分析分时数据.
     *
     * @param string $from
     * @param string $to
     *
     * @return mixed
     */
    public function interfaceSummaryHourly(string $from, string $to)
    {
        return $this->query('datacube/getinterfacesummaryhour', $from, $to);
    }

    /**
     * 拉取卡券概况数据接口.
     *
     * @param string $from
     * @param string $to
     * @param int    $condSource
     *
     * @return mixed
     */
    public function cardSummary(string $from, string $to, $condSource = 0)
    {
        $ext = [
            'cond_source' => intval($condSource),
        ];

        return $this->query('datacube/getcardbizuininfo', $from, $to, $ext);
    }

    /**
     * 获取免费券数据接口.
     *
     * @param string $from
     * @param string $to
     * @param int    $condSource
     * @param string $cardId
     *
     * @return mixed
     */
    public function freeCardSummary(string $from, string $to, int $condSource = 0, string $cardId = '')
    {
        $ext = [
            'cond_source' => intval($condSource),
            'card_id' => $cardId,
        ];

        return $this->query('datacube/getcardcardinfo', $from, $to, $ext);
    }

    /**
     * 拉取会员卡数据接口.
     *
     * @param string $from
     * @param string $to
     * @param int    $condSource
     *
     * @return mixed
     */
    public function memberCardSummary(string $from, string $to, $condSource = 0)
    {
        $ext = [
            'cond_source' => intval($condSource),
        ];

        return $this->query('datacube/getcardmembercardinfo', $from, $to, $ext);
    }

    /**
     * 拉取单张会员卡数据接口.
     *
     * @param string $from
     * @param string $to
     * @param string $cardId
     *
     * @return mixed
     */
    public function memberCardSummaryById(string $from, string $to, string $cardId)
    {
        $ext = [
            'card_id' => $cardId,
        ];

        return $this->query('datacube/getcardmembercarddetail', $from, $to, $ext);
    }

    /**
     * 查询数据.
     *
     * @param string $api
     * @param string $from
     * @param string $to
     * @param array  $ext
     *
     * @return mixed
     */
    protected function query(string $api, string $from, string $to, array $ext = [])
    {
        $params = array_merge([
            'begin_date' => $from,
            'end_date' => $to,
        ], $ext);

        return $this->httpPostJson($api, $params);
    }
}
