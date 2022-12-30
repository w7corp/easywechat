<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\Auth;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author lujunyi <lujunyi@shopex.cn>
 */
class Client extends BaseClient
{
    /**
     * 获取网页授权登录url，适用于自建应用与代开发应用。
     *
     * @see https://developer.work.weixin.qq.com/document/path/91022
     *
     * @param string $redirectUri
     * @param string $scope
     * @param string $agentId
     * @param string|null $state
     *
     * @return string
     * @throws \Exception
     */
    public function getOAuthRedirectUrl(string $redirectUri = '', string $scope = 'snsapi_privateinfo', string $agentId = '', string $state = null)
    {
        $redirectUri || $redirectUri = $this->app->config['redirect_uri_oauth'];
        $state || $state = random_bytes(64);
        $params = [
            'appid' => $this->app['config']['suite_id'],
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => $scope,
            'state' => $state,
            'agentid' => $agentId,
        ];

        return 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query($params) . '#wechat_redirect';
    }


    /**
     * 获取访问用户身份。根据code获取成员信息，适用于自建应用与代开发应用。
     *
     * @see https://developer.work.weixin.qq.com/document/path/91023
     *
     * @param string $code
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserByCode(string $code)
    {
        $params = [
            'code' => $code,
        ];

        return $this->httpGet('cgi-bin/auth/getuserinfo', $params);
    }

    /**
     * 获取访问用户敏感信息。自建应用与代开发应用可通过该接口获取成员授权的敏感字段。
     *
     * @see https://developer.work.weixin.qq.com/document/path/95833
     *
     * @param string $userTicket
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserByTicket(string $userTicket)
    {
        $params = [
            'user_ticket' => $userTicket,
        ];

        return $this->httpPostJson('cgi-bin/auth/getuserdetail', $params);
    }
}
