<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform\Base;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * Get authorization info.
     *
     * @param string|null $authCode
     *
     * @return mixed
     */
    public function handleAuthorize(string $authCode = null)
    {
        $params = [
            'component_appid' => $this->app['config']['app_id'],
            'authorization_code' => $authCode ?? $this->app['request']->get('auth_code'),
        ];

        return $this->httpPostJson('cgi-bin/component/api_query_auth', $params);
    }

    /**
     * Get authorizer info.
     *
     * @param string $appId
     *
     * @return mixed
     */
    public function getAuthorizer(string $appId)
    {
        $params = [
            'component_appid' => $this->app['config']['app_id'],
            'authorizer_appid' => $appId,
        ];

        return $this->httpPostJson('cgi-bin/component/api_get_authorizer_info', $params);
    }

    /**
     * Get options.
     *
     * @param string $appId
     * @param string $name
     *
     * @return mixed
     */
    public function getAuthorizerOption(string $appId, string $name)
    {
        $params = [
            'component_appid' => $this->app['config']['app_id'],
            'authorizer_appid' => $appId,
            'option_name' => $name,
        ];

        return $this->httpPostJson('cgi-bin/component/api_get_authorizer_option', $params);
    }

    /**
     * Set authorizer option.
     *
     * @param string $appId
     * @param string $name
     * @param string $value
     *
     * @return mixed
     */
    public function setAuthorizerOption(string $appId, string $name, string $value)
    {
        $params = [
            'component_appid' => $this->app['config']['app_id'],
            'authorizer_appid' => $appId,
            'option_name' => $name,
            'option_value' => $value,
        ];

        return $this->httpPostJson('cgi-bin/component/api_set_authorizer_option', $params);
    }

    /**
     * Get authorizer list.
     *
     * @param int $offset
     * @param int $count
     *
     * @return mixed
     */
    public function getAuthorizers($offset = 0, $count = 500)
    {
        $params = [
            'component_appid' => $this->app['config']['app_id'],
            'offset' => $offset,
            'count' => $count,
        ];

        return $this->httpPostJson('cgi-bin/component/api_get_authorizer_list', $params);
    }

    /**
     * Create pre-authorization code.
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function createPreAuthorizationCode()
    {
        $params = [
            'component_appid' => $this->app['config']['app_id'],
        ];

        return $this->httpPostJson('cgi-bin/component/api_create_preauthcode', $params);
    }

    /**
     * OpenPlatform Clear quota.
     *
     * @see https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419318587
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function clearQuota()
    {
        $params = [
            'component_appid' => $this->app['config']['app_id'],
        ];

        return $this->httpPostJson('cgi-bin/component/clear_quota', $params);
    }
}
