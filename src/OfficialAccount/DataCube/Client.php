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
    public function userSummary($from, $to)
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
    public function userCumulate($from, $to)
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
    public function articleSummary($from, $to)
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
    public function articleTotal($from, $to)
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
    public function userReadSummary($from, $to)
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
    public function userReadHourly($from, $to)
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
    public function userShareSummary($from, $to)
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
    public function userShareHourly($from, $to)
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
    public function upstreamMessageSummary($from, $to)
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
    public function upstreamMessageHourly($from, $to)
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
    public function upstreamMessageWeekly($from, $to)
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
    public function upstreamMessageMonthly($from, $to)
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
    public function upstreamMessageDistSummary($from, $to)
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
    public function upstreamMessageDistWeekly($from, $to)
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
    public function upstreamMessageDistMonthly($from, $to)
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
    public function interfaceSummary($from, $to)
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
    public function interfaceSummaryHourly($from, $to)
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
    public function cardSummary($from, $to, $condSource = 0)
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
    public function freeCardSummary($from, $to, $condSource = 0, $cardId = '')
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
    public function memberCardSummary($from, $to, $condSource = 0)
    {
        $ext = [
            'cond_source' => intval($condSource),
        ];

        return $this->query('datacube/getcardmembercardinfo', $from, $to, $ext);
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
    protected function query($api, $from, $to, array $ext = [])
    {
        $params = array_merge([
            'begin_date' => $from,
            'end_date' => $to,
        ], $ext);

        return $this->httpPostJson($api, $params);
    }
}
