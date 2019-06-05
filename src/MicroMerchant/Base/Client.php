<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MicroMerchant\Base;

use EasyWeChat\MicroMerchant\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * apply to settle in to become a small micro merchant.
     *
     * @param  $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\EncryptException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidExtensionException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidSignException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function applyForEnter(array $params)
    {
        $params = $this->processingParams(array_merge($params, [
            'version'   => '3.0',
            'cert_sn'   => '',
            'sign_type' => 'HMAC-SHA256',
            'nonce_str' => uniqid('micro'),
        ]));
        return $this->safeRequest('applyment/micro/submit', $params);
    }

    /**
     * query application status.
     *
     * @param  string  $applyment_id
     * @param  string  $business_code
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidSignException
     */
    public function getState(string $applyment_id, string $business_code = '')
    {
        if (!empty($applyment_id)) {
            $params = [
                'applyment_id' => $applyment_id,
            ];
        } else {
            $params = [
                'business_code' => $business_code,
            ];
        }

        $params = array_merge($params, [
            'version'   => '1.0',
            'sign_type' => 'HMAC-SHA256',
            'nonce_str' => uniqid('micro'),
        ]);
        return $this->safeRequest('applyment/micro/getstate', $params);
    }

    /**
     * merchant upgrade api.
     *
     * @param  array  $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\EncryptException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidExtensionException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidSignException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function upgrade(array $params)
    {
        $params['sub_mch_id'] = $params['sub_mch_id'] ?? $this->app['config']->sub_mch_id;
        $params               = $this->processingParams(array_merge($params, [
            'version'   => '1.0',
            'cert_sn'   => '',
            'sign_type' => 'HMAC-SHA256',
            'nonce_str' => uniqid('micro'),
        ]));
        return $this->safeRequest('applyment/micro/submitupgrade', $params);
    }

    /**
     * get upgrade state.
     *
     * @param  string  $sub_mch_id
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidSignException
     */
    public function getUpgradeState(string $sub_mch_id = '')
    {
        return $this->safeRequest('applyment/micro/getupgradestate', [
            'version'    => '1.0',
            'sign_type'  => 'HMAC-SHA256',
            'sub_mch_id' => $sub_mch_id ? : $this->app['config']->sub_mch_id,
            'nonce_str'  => uniqid('micro'),
        ]);
    }
}
