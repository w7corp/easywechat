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
 * BaseApi.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @author    lixiao <leonlx126@gmail.com>
 * @copyright 2016
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\OpenPlatform\Api;

class BaseApi extends AbstractOpenPlatform
{
    /**
     * Get auth info api.
     */
    const GET_AUTH_INFO = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth';

    /**
     * Get authorizer token api.
     */
    const GET_AUTHORIZER_TOKEN = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token';

    /**
     * Get authorizer info api.
     */
    const GET_AUTHORIZER_INFO = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info';

    /**
     * Get authorizer options api.
     */
    const GET_AUTHORIZER_OPTION = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_option';

    /**
     * Set authorizer options api.
     */
    const SET_AUTHORIZER_OPTION = 'https://api.weixin.qq.com/cgi-bin/component/api_set_authorizer_option';

    /**
     * Get authorization info.
     *
     * @param $authCode
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getAuthorizationInfo($authCode = null)
    {
        $params = [
            'component_appid' => $this->getAppId(),
            'authorization_code' => $authCode ?: $this->request->get('auth_code'),
        ];

        return $this->parseJSON('json', [self::GET_AUTH_INFO, $params]);
    }

    /**
     * Get authorizer token.
     *
     * It doesn't cache the authorizer-access-token.
     * So developers should NEVER call this method.
     * It'll called by: AuthorizerAccessToken::renewAccessToken()
     *
     * @param $appId
     * @param $refreshToken
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getAuthorizerToken($appId, $refreshToken)
    {
        $params = [
            'component_appid' => $this->getAppId(),
            'authorizer_appid' => $appId,
            'authorizer_refresh_token' => $refreshToken,
        ];

        return $this->parseJSON('json', [self::GET_AUTHORIZER_TOKEN, $params]);
    }

    /**
     * Get authorizer info.
     *
     * @param string $authorizerAppId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getAuthorizerInfo($authorizerAppId)
    {
        $params = [
            'component_appid' => $this->getAppId(),
            'authorizer_appid' => $authorizerAppId,
        ];

        return $this->parseJSON('json', [self::GET_AUTHORIZER_INFO, $params]);
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
        $params = [
            'component_appid' => $this->getAppId(),
            'authorizer_appid' => $authorizerAppId,
            'option_name' => $optionName,
        ];

        return $this->parseJSON('json', [self::GET_AUTHORIZER_OPTION, $params]);
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
        $params = [
            'component_appid' => $this->getAppId(),
            'authorizer_appid' => $authorizerAppId,
            'option_name' => $optionName,
            'option_value' => $optionValue,
        ];

        return $this->parseJSON('json', [self::SET_AUTHORIZER_OPTION, $params]);
    }
}
