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
 * Class Client.
 *
 * @author her-cat <i@her-cat.com>
 */
class Client extends BaseClient
{
    /**
     * Get Wi-Fi statistics.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function summary(string $beginDate, string $endDate, int $shopId = -1)
    {
        $data = [
            'begin_date' => $beginDate,
            'end_date' => $endDate,
            'shop_id' => $shopId,
        ];

        return $this->httpPostJson('bizwifi/statistics/list', $data);
    }

    /**
     * Get the material QR code.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getQrCodeUrl(int $shopId, string $ssid, int $type = 0)
    {
        $data = [
            'shop_id' => $shopId,
            'ssid' => $ssid,
            'img_id' => $type,
        ];

        return $this->httpPostJson('bizwifi/qrcode/get', $data);
    }

    /**
     * Wi-Fi completion page jump applet.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setFinishPage(array $data)
    {
        return $this->httpPostJson('bizwifi/finishpage/set', $data);
    }

    /**
     * Set the top banner jump applet.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setHomePage(array $data)
    {
        return $this->httpPostJson('bizwifi/homepage/set', $data);
    }
}
