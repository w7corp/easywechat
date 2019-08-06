<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MicroMerchant\Material;

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
     * update settlement card.
     *
     * @param $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\EncryptException
     */
    public function setSettlementCard($params)
    {
        $params['sub_mch_id'] = $params['sub_mch_id'] ?? $this->app['config']->sub_mch_id;
        $params = $this->processParams(array_merge($params, [
            'version' => '1.0',
            'cert_sn' => '',
            'sign_type' => 'HMAC-SHA256',
            'nonce_str' => uniqid('micro'),
        ]));

        return $this->safeRequest('applyment/micro/modifyarchives', $params);
    }

    /**
     * update contact info.
     *
     * @param $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\EncryptException
     */
    public function updateContact($params)
    {
        $params['sub_mch_id'] = $params['sub_mch_id'] ?? $this->app['config']->sub_mch_id;
        $params = $this->processParams(array_merge($params, [
            'version' => '1.0',
            'cert_sn' => '',
            'sign_type' => 'HMAC-SHA256',
            'nonce_str' => uniqid('micro'),
        ]));

        return $this->safeRequest('applyment/micro/modifycontactinfo', $params);
    }
}
