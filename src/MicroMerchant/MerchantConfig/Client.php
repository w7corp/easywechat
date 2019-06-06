<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MicroMerchant\MerchantConfig;

use EasyWeChat\MicroMerchant\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author   liuml  <liumenglei0211@163.com>
 * @DateTime 2019-05-30  14:19
 */
class Client extends BaseClient
{
    /**
     * Service providers configure recommendation functions for small and micro businesses.
     *
     * @param string $sub_appid
     * @param string $subscribe_appid
     * @param string $receipt_appid
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidSignException
     */
    public function attention(string $sub_appid, string $subscribe_appid, string $receipt_appid = '')
    {
        $params = [
            'sub_appid' => $sub_appid,
        ];

        if (!empty($subscribe_appid)) {
            $params['subscribe_appid'] = $subscribe_appid;
        } else {
            $params['receipt_appid'] = $receipt_appid;
        }

        return $this->safeRequest('secapi/mkt/addrecommendconf', array_merge($params, [
            'sign_type' => 'HMAC-SHA256',
            'nonce_str' => uniqid('micro'),
        ]));
    }

    /**
     * Configure the new payment directory.
     *
     * @param string $jsapiPath
     * @param string $appid
     * @param string $subMchId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidSignException
     */
    public function addPayAccreditDirectory(string $jsapiPath, string $appid = '', string $subMchId = '')
    {
        return $this->addSubDevConfig([
            'appid' => $appid ?: $this->app['config']->appid,
            'sub_mch_id' => $subMchId ?: $this->app['config']->sub_mch_id,
            'jsapi_path' => $jsapiPath,
        ]);
    }

    /**
     * bind appid.
     *
     * @param string $subAppid
     * @param string $appid
     * @param string $subMchId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidSignException
     */
    public function bindAppid(string $subAppid, string $appid = '', string $subMchId = '')
    {
        return $this->addSubDevConfig([
            'appid' => $appid ?: $this->app['config']->appid,
            'sub_mch_id' => $subMchId ?: $this->app['config']->sub_mch_id,
            'sub_appid' => $subAppid,
        ]);
    }

    /**
     * addSubDevConfig.
     *
     * @param $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidSignException
     */
    private function addSubDevConfig($params)
    {
        return $this->safeRequest('secapi/mch/addsubdevconfig', $params);
    }

    /**
     * querySubDevConfig.
     *
     * @param $sub_mch_id
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidSignException
     */
    public function querySubDevConfig($sub_mch_id = '')
    {
        return $this->safeRequest('secapi/mch/querysubdevconfig', [
            'sub_mch_id' => $sub_mch_id ?: $this->app['config']->sub_mch_id,
        ]);
    }
}
