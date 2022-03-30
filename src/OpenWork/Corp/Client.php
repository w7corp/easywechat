<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenWork\Corp;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\ServiceContainer;

/**
 * Client.
 *
 * @author xiaomin <keacefull@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * Client constructor.
     * 三方接口有三个access_token，这里用的是suite_access_token.
     *
     * @param ServiceContainer $app
     */
    public function __construct(ServiceContainer $app)
    {
        parent::__construct($app, $app['suite_access_token']);
    }

    /**
     * 企业微信安装应用授权 url.
     *
     * @param string $preAuthCode 预授权码
     * @param string $redirectUri 回调地址
     * @param string $state
     *
     * @return string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function getPreAuthorizationUrl(string $preAuthCode = '', string $redirectUri = '', string $state = '')
    {
        $redirectUri || $redirectUri = $this->app->config['redirect_uri_install'];
        $preAuthCode || $preAuthCode = $this->getPreAuthCode()['pre_auth_code'];
        $state || $state = random_bytes(64);

        $params = [
            'suite_id' => $this->app['config']['suite_id'],
            'redirect_uri' => $redirectUri,
            'pre_auth_code' => $preAuthCode,
            'state' => $state,
        ];

        return 'https://open.work.weixin.qq.com/3rdapp/install?' . http_build_query($params);
    }

    /**
     * 获取预授权码.
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPreAuthCode()
    {
        return $this->httpGet('cgi-bin/service/get_pre_auth_code');
    }

    /**
     * 设置授权配置.
     * 该接口可对某次授权进行配置.
     *
     * @param string $preAuthCode
     * @param array $sessionInfo
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setSession(string $preAuthCode, array $sessionInfo)
    {
        $params = [
            'pre_auth_code' => $preAuthCode,
            'session_info' => $sessionInfo,
        ];

        return $this->httpPostJson('cgi-bin/service/set_session_info', $params);
    }

    /**
     * 获取企业永久授权码.
     *
     * @param string $authCode 临时授权码，会在授权成功时附加在redirect_uri中跳转回第三方服务商网站，或通过回调推送给服务商。长度为64至512个字节
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPermanentByCode(string $authCode)
    {
        $params = [
            'auth_code' => $authCode,
        ];

        return $this->httpPostJson('cgi-bin/service/get_permanent_code', $params);
    }

    /**
     * 获取企业授权信息.
     *
     * @param string $authCorpId
     * @param string $permanentCode
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAuthorization(string $authCorpId, string $permanentCode)
    {
        $params = [
            'auth_corpid' => $authCorpId,
            'permanent_code' => $permanentCode,
        ];

        return $this->httpPostJson('cgi-bin/service/get_auth_info', $params);
    }

    /**
     * 获取应用的管理员列表.
     *
     * @param string $authCorpId
     * @param string $agentId
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getManagers(string $authCorpId, string $agentId)
    {
        $params = [
            'auth_corpid' => $authCorpId,
            'agentid' => $agentId,
        ];

        return $this->httpPostJson('cgi-bin/service/get_admin_list', $params);
    }

    /**
     * 获取登录url.
     *
     * @param string $redirectUri
     * @param string $scope
     * @param string|null $state
     *
     * @return string
     * @throws \Exception
     */
    public function getOAuthRedirectUrl(string $redirectUri = '', string $scope = 'snsapi_userinfo', string $state = null)
    {
        $redirectUri || $redirectUri = $this->app->config['redirect_uri_oauth'];
        $state || $state = random_bytes(64);
        $params = [
            'appid' => $this->app['config']['suite_id'],
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => $scope,
            'state' => $state,
        ];

        return 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query($params) . '#wechat_redirect';
    }

    /**
     * 第三方根据code获取企业成员信息.
     *
     * @param string $code
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserByCode(string $code)
    {
        $params = [
            'code' => $code,
        ];

        return $this->httpGet('cgi-bin/service/getuserinfo3rd', $params);
    }

    /**
     * 第三方使用user_ticket获取成员详情.
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

        return $this->httpPostJson('cgi-bin/service/getuserdetail3rd', $params);
    }

    /**
     * 第三方根据unionid查询external_userid
     *
     * @param string $unionid 微信用户的unionid
     * @param string $openid 微信用户的openid
     * @param string $corpid 需要换取的企业corpid，不填则拉取所有企业
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @author SinyLi <yxlix1@163.com>
     */
    public function unionidToExternalUserid(string $unionid, string $openid, string $corpid = '')
    {
        $params = [
            'unionid' => $unionid,
            'openid' => $openid,
            'corpid' => $corpid
        ];

        return $this->httpPostJson('cgi-bin/service/externalcontact/unionid_to_external_userid_3rd', $params);
    }
}
