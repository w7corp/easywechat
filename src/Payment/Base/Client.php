<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Payment\Base;

use EasyWeChat\Payment\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * Pay the order.
     *
     * @param array $params
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function pay(array $params)
    {
        $params['appid'] = $this->app['config']->app_id;

        return $this->request($this->wrap('pay/micropay'), $params);
    }

    /**
     * Get openid by auth code.
     *
     * @param string $authCode
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function authCodeToOpenid(string $authCode)
    {
        return $this->request('tools/authcodetoopenid', [
            'appid' => $this->app['config']->app_id,
            'auth_code' => $authCode,
        ]);
    }
}
