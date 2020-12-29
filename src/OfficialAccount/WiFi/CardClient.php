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
 * Class CardClient.
 *
 * @author her-cat <i@her-cat.com>
 */
class CardClient extends BaseClient
{
    /**
     * Set shop card coupon delivery information.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function set(array $data)
    {
        return $this->httpPostJson('bizwifi/couponput/set', $data);
    }

    /**
     * Get shop card coupon delivery information.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(int $shopId = 0)
    {
        return $this->httpPostJson('bizwifi/couponput/get', ['shop_id' => $shopId]);
    }
}
