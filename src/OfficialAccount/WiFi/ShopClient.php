<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\WiFi;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class ShopClient.
 *
 * @author her-cat <i@her-cat.com>
 */
class ShopClient extends BaseClient
{
    /**
     * Get shop Wi-Fi information.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(int $shopId)
    {
        return $this->httpPostJson('bizwifi/shop/get', ['shop_id' => $shopId]);
    }

    /**
     * Get a list of Wi-Fi shops.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list(int $page = 1, int $size = 10)
    {
        $data = [
            'pageindex' => $page,
            'pagesize' => $size,
        ];

        return $this->httpPostJson('bizwifi/shop/list', $data);
    }

    /**
     * Update shop Wi-Fi information.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(int $shopId, array $data)
    {
        $data = array_merge(['shop_id' => $shopId], $data);

        return $this->httpPostJson('bizwifi/shop/update', $data);
    }

    /**
     * Clear shop network and equipment.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function clearDevice(int $shopId, string $ssid = null)
    {
        $data = [
            'shop_id' => $shopId,
        ];

        if (!is_null($ssid)) {
            $data['ssid'] = $ssid;
        }

        return $this->httpPostJson('bizwifi/shop/clean', $data);
    }
}
