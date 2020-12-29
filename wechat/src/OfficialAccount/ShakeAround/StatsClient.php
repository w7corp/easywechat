<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\ShakeAround;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class StatsClient.
 *
 * @author allen05ren <allen05ren@outlook.com>
 */
class StatsClient extends BaseClient
{
    /**
     * Fetch statistics data by deviceId.
     *
     * @param array $deviceIdentifier
     * @param int   $beginTime        (Unix timestamp)
     * @param int   $endTime          (Unix timestamp)
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deviceSummary(array $deviceIdentifier, int $beginTime, int $endTime)
    {
        $params = [
            'device_identifier' => $deviceIdentifier,
            'begin_date' => $beginTime,
            'end_date' => $endTime,
        ];

        return $this->httpPostJson('shakearound/statistics/device', $params);
    }

    /**
     * Fetch all devices statistics data by date.
     *
     * @param int $timestamp
     * @param int $pageIndex
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function devicesSummary(int $timestamp, int $pageIndex)
    {
        $params = [
            'date' => $timestamp,
            'page_index' => $pageIndex,
        ];

        return $this->httpPostJson('shakearound/statistics/devicelist', $params);
    }

    /**
     * Fetch statistics data by pageId.
     *
     * @param int $pageId
     * @param int $beginTime (Unix timestamp)
     * @param int $endTime   (Unix timestamp)
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pageSummary(int $pageId, int $beginTime, int $endTime)
    {
        $params = [
            'page_id' => $pageId,
            'begin_date' => $beginTime,
            'end_date' => $endTime,
        ];

        return $this->httpPostJson('shakearound/statistics/page', $params);
    }

    /**
     * Fetch all pages statistics data by date.
     *
     * @param int $timestamp
     * @param int $pageIndex
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pagesSummary(int $timestamp, int $pageIndex)
    {
        $params = [
            'date' => $timestamp,
            'page_index' => $pageIndex,
        ];

        return $this->httpPostJson('shakearound/statistics/pagelist', $params);
    }
}
