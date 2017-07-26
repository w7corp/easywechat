<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform\Account;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author Scholer <scholer_l@live.com>
 */
class Client extends BaseClient
{
    /**
     * @var string
     */
    protected $baseUri = 'https://api.weixin.qq.com/cgi-bin/open/';

    /**
     * 创建开放平台帐号并绑定公众号/小程序.
     *
     * @param string $appId 授权公众号或小程序的appid
     *
     * @return mixed
     */
    public function create(string $appId)
    {
        $params = [
            'appid' => $appId,
        ];

        return $this->httpPostJson('create', $params);
    }

    /**
     * 将公众号/小程序绑定到开放平台帐号下.
     *
     * @param string $appId     授权公众号或小程序的appid
     * @param string $openAppId 开放平台帐号appid
     *
     * @return mixed
     */
    public function bind(string $appId, string $openAppId)
    {
        $params = [
            'appid' => $appId,
            'open_appid' => $openAppId,
        ];

        return $this->httpPostJson('bind', $params);
    }

    /**
     * 将公众号/小程序从开放平台帐号下解绑.
     *
     * @param string $appId     授权公众号或小程序的appid
     * @param string $openAppId 开放平台帐号appid
     *
     * @return mixed
     */
    public function unbind(string $appId, string $openAppId)
    {
        $params = [
            'appid' => $appId,
            'open_appid' => $openAppId,
        ];

        return $this->httpPostJson('unbind', $params);
    }

    /**
     * 获取公众号/小程序所绑定的开放平台帐号.
     *
     * @param string $appId 授权公众号或小程序的appid
     *
     * @return mixed
     */
    public function getBinding(string $appId)
    {
        $params = [
            'appid' => $appId,
        ];

        return $this->httpPostJson('get', $params);
    }
}
