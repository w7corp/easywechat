<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\WiFi;

use EasyWeChat\Kernel\BaseClient;

class CardClient extends BaseClient
{
    /**
     * Set shop card coupon delivery information.
     *
     * @param array $data
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
     * @param int $shopId
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
