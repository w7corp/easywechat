<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MiniProgram\OpenData;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author tianyong90 <412039588@qq.com>
 */
class Client extends BaseClient
{
    /**
     * @var string
     */
    protected $baseUri = 'https://api.weixin.qq.com/wxa/';

    /**
     * removeUserStorage.
     *
     * @param string $openid
     * @param string $sessionKey
     * @param array  $key
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function removeUserStorage(string $openid, string $sessionKey, array $key)
    {
        $data = ['key' => $key];
        $query = [
            'appid' => $this->app['config']['app_id'],
            'secret' => $this->app['config']['secret'],
            'openid' => $openid,
            'sig_method' => 'hmac_sha256',
            'signature' => hash_hmac('sha256', json_encode($data), $sessionKey),
        ];

        return $this->httpPostJson('remove_user_storage', $data, $query);
    }

    /**
     * setUserStorage.
     *
     * @param string $openid
     * @param string $sessionKey
     * @param array  $kvList
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function setUserStorage(string $openid, string $sessionKey, array $kvList)
    {
        $data = ['kv_list' => $kvList];
        $query = [
            'appid' => $this->app['config']['app_id'],
            'secret' => $this->app['config']['secret'],
            'openid' => $openid,
            'sig_method' => 'hmac_sha256',
            'signature' => hash_hmac('sha256', json_encode($data), $sessionKey),
        ];

        return $this->httpPostJson('set_user_storage', $data, $query);
    }
}
