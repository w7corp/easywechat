<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\Store;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author bigface <saybye720@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * Get WXA supported categories.
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function categories()
    {
        return $this->httpGet('wxa/get_merchant_category');
    }

    /**
     * Get district from tencent map .
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function districts()
    {
        return $this->httpGet('wxa/get_district');
    }

    /**
     * Search store from tencent map.
     *
     * @param int    $districtId
     * @param string $keyword
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function searchFromMap(int $districtId, string $keyword)
    {
        $params = [
            'districtid' => $districtId,
            'keyword' => $keyword,
        ];

        return $this->httpPostJson('wxa/search_map_poi', $params);
    }

    /**
     * Get store check status.
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getStatus()
    {
        return $this->httpGet('wxa/get_merchant_audit_info');
    }

    /**
     * Create a merchant.
     *
     * @param array $baseInfo
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createMerchant(array $baseInfo)
    {
        return $this->httpPostJson('wxa/apply_merchant', $baseInfo);
    }

    /**
     * Update a merchant.
     *
     * @param array $params
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateMerchant(array $params)
    {
        return $this->httpPostJson('wxa/modify_merchant', $params);
    }

    /**
     * Create a store from tencent map.
     *
     * @param array $baseInfo
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createFromMap(array $baseInfo)
    {
        return $this->httpPostJson('wxa/create_map_poi', $baseInfo);
    }

    /**
     * Create a store.
     *
     * @param array $baseInfo
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(array $baseInfo)
    {
        return $this->httpPostJson('wxa/add_store', $baseInfo);
    }

    /**
     * Update a store.
     *
     * @param int   $poiId
     * @param array $baseInfo
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(int $poiId, array $baseInfo)
    {
        $params = array_merge($baseInfo, ['poi_id' => $poiId]);

        return $this->httpPostJson('wxa/update_store', $params);
    }

    /**
     * Get store by ID.
     *
     * @param int $poiId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(int $poiId)
    {
        return $this->httpPostJson('wxa/get_store_info', ['poi_id' => $poiId]);
    }

    /**
     * List store.
     *
     * @param int $offset
     * @param int $limit
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list(int $offset = 0, int $limit = 10)
    {
        $params = [
            'offset' => $offset,
            'limit' => $limit,
        ];

        return $this->httpPostJson('wxa/get_store_list', $params);
    }

    /**
     * Delete a store.
     *
     * @param int $poiId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(int $poiId)
    {
        return $this->httpPostJson('wxa/del_store', ['poi_id' => $poiId]);
    }
}
