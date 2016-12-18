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
 * @author    allen05ren <allen05ren@outlook.com>
 * @copyright 2016 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */
namespace EasyWeChat\ShakeAround;

use EasyWeChat\Core\AbstractAPI;

/**
 * Class Stats.
 */
class Stats extends AbstractAPI
{
    const API_DEVICE = 'https://api.weixin.qq.com/shakearound/statistics/device';
    const API_DEVICE_LIST = 'https://api.weixin.qq.com/shakearound/statistics/devicelist';
    const API_PAGE = 'https://api.weixin.qq.com/shakearound/statistics/page';
    const API_PAGE_LIST = 'https://api.weixin.qq.com/shakearound/statistics/pagelist';

    /**
     * Fetch statistics data by device_id.
     *
     * @param array $device_identifier
     * @param int   $begin_date (Unix timestamp)
     * @param int   $end_date (Unix timestamp)
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function deviceSummary(array $device_identifier, $begin_date, $end_date)
    {
        $params = [
            'device_identifier' => $device_identifier,
            'begin_date' => $begin_date,
            'end_date' => $end_date,
        ];

        return $this->parseJSON('json', [self::API_DEVICE, $params]);
    }

    /**
     * Fetch all devices statistics data by date.
     *
     * @param int $timestamp
     * @param int $page_index
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function batchDeviceSummary($timestamp, $page_index)
    {
        $params = [
            'date' => $timestamp,
            'page_index' => $page_index,
        ];

        return $this->parseJSON('json', [self::API_DEVICE_LIST, $params]);
    }

    /**
     * Fetch statistics data by page_id.
     *
     * @param int $page_id
     * @param int $begin_date (Unix timestamp)
     * @param int $end_date (Unix timestamp)
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function pageSummary($page_id, $begin_date, $end_date)
    {
        $params = [
            'page_id' => $page_id,
            'begin_date' => $begin_date,
            'end_date' => $end_date,
        ];
        return $this->parseJSON('json', [self::API_PAGE, $params]);
    }

    /**
     * Fetch all pages statistics data by date.
     *
     * @param int $timestamp
     * @param int $page_index
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function batchPageSummary($timestamp, $page_index)
    {
        $params = [
            'date' => $timestamp,
            'page_index' => $page_index,
        ];

        return $this->parseJSON('json', [self::API_PAGE_LIST, $params]);
    }
}
