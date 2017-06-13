<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\OpenPlatform\Base;

use EasyWeChat\Support\HasHttpRequests;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client
{
    use HasHttpRequests;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * Constructor.
     *
     * @param string $clientId
     */
    public function __construct(string $clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * Get authorization info.
     *
     * @param string|null $authCode
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getAuthorizationInfo(string $authCode = null)
    {
        $params = [
            'component_appid' => $this->clientId,
            'authorization_code' => $authCode ?: $this->request->get('auth_code'),
        ];

        return $this->parseJSON(
            $this->postJson('https://api.weixin.qq.com/cgi-bin/component/api_query_auth', $params)
        );
    }

    /**
     * Get authorizer info.
     *
     * @param string $appId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getAuthorizerInfo(string $appId)
    {
        $params = [
            'component_appid' => $this->clientId,
            'authorizer_appid' => $appId,
        ];

        return $this->parseJSON('json', ['https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info', $params]);
    }

    /**
     * Get options.
     *
     * @param string $appId
     * @param string $key
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getAuthorizerOption(string $appId, string $key)
    {
        $params = [
            'component_appid' => $this->clientId,
            'authorizer_appid' => $appId,
            'option_name' => $key,
        ];

        return $this->parseJSON('json', ['https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_option', $params]);
    }

    /**
     * Set authorizer option.
     *
     * @param string $appId
     * @param string $key
     * @param string $value
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function setAuthorizerOption(string $appId, string $key, string $value)
    {
        $params = [
            'component_appid' => $this->clientId,
            'authorizer_appid' => $appId,
            'option_name' => $key,
            'option_value' => $value,
        ];

        return $this->parseJSON('json', ['https://api.weixin.qq.com/cgi-bin/component/api_set_authorizer_option', $params]);
    }
}
