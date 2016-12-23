<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Authorizer.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @copyright 2016
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\OpenPlatform\Components;

class Authorizer extends AbstractComponent
{
    /**
     * Get auth info api.
     */
    const GET_AUTH_INFO = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth';

    /**
     * Authorization token api.
     */
    const AUTHORIZATION_TOKEN = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token';

    /**
     * Get authorizer info api.
     */
    const GET_AUTHORIZER_INFO = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info';

    /**
     * Get authorizer options api.
     */
    const GET_AUTHORIZER_OPTION = 'https://api.weixin.qq.com/cgi-bin/component/ api_get_authorizer_option';

    /**
     * Set authorizer options api.
     */
    const SET_AUTHORIZER_OPTION = 'https://api.weixin.qq.com/cgi-bin/component/ api_set_authorizer_option';

    /**
     * Get authorizer info.
     *
     * @param $authorizationCode
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getAuthInfo($authorizationCode = null)
    {
        $data = [
            'component_appid' => $this->getComponentAppId(),
            'authorization_code' => $authorizationCode ?: $this->request->get('auth_code'),
        ];

        return $this->parseJSON('json', [self::GET_AUTH_INFO, $data]);
    }

    /**
     * Get authorization token.
     *
     * @param $appId
     * @param $refreshToken
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getAuthorizationToken($appId, $refreshToken)
    {
        $data = [
            'component_appid' => $this->getComponentAppId(),
            'authorizer_appid' => $appId,
            'authorizer_refresh_token' => $refreshToken,
        ];

        return $this->parseJSON('json', [self::AUTHORIZATION_TOKEN, $data]);
    }

    /**
     * Get authorizer info.
     *
     * @param $authorizerAppId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getAuthorizerInfo($authorizerAppId)
    {
        $data = [
            'component_appid' => $this->getComponentAppId(),
            'authorizer_appid' => $authorizerAppId,
        ];

        return $this->parseJSON('json', [self::GET_AUTHORIZER_INFO, $data]);
    }

    /**
     * Get options.
     *
     * @param $authorizerAppId
     * @param $optionName
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getAuthorizerOption($authorizerAppId, $optionName)
    {
        $data = [
            'component_appid' => $this->getComponentAppId(),
            'authorizer_appid' => $authorizerAppId,
            'option_name' => $optionName,
        ];

        return $this->parseJSON('json', [self::GET_AUTHORIZER_OPTION, $data]);
    }

    /**
     * Set authorizer option.
     *
     * @param $authorizerAppId
     * @param $optionName
     * @param $optionValue
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function setAuthorizerOption($authorizerAppId, $optionName, $optionValue)
    {
        $data = [
            'component_appid' => $this->getComponentAppId(),
            'authorizer_appid' => $authorizerAppId,
            'option_name' => $optionName,
            'option_value' => $optionValue,
        ];

        return $this->parseJSON('json', [self::SET_AUTHORIZER_OPTION, $data]);
    }

    /**
     * Get component appId.
     *
     * @return string
     */
    private function getComponentAppId()
    {
        return $this->config['app_id'];
    }
}
