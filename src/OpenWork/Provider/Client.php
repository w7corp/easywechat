<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenWork\Provider;

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
     *
     *
     * @param ServiceContainer $app
     */
    public function __construct(ServiceContainer $app)
    {
        parent::__construct($app, $app['provider_access_token']);
    }

    /**
     * 单点登录 - 获取登录的地址.
     *
     * @param string $redirectUri
     * @param string $userType
     * @param string $state
     *
     * @return string
     */
    public function getLoginUrl(string $redirectUri = '', string $userType = 'admin', string $state = '')
    {
        $redirectUri || $redirectUri = $this->app->config['redirect_uri_single'];
        $state || $state = rand();
        $params = [
            'appid' => $this->app['config']['corp_id'],
            'redirect_uri' => $redirectUri,
            'usertype' => $userType,
            'state' => $state,
        ];

        return 'https://open.work.weixin.qq.com/wwopen/sso/3rd_qrConnect?'.http_build_query($params);
    }

    /**
     * 单点登录 - 获取登录用户信息.
     *
     * @param string $authCode
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getLoginInfo(string $authCode)
    {
        $params = [
            'auth_code' => $authCode,
        ];

        return $this->httpPostJson('cgi-bin/service/get_login_info', $params);
    }

    /**
     * 获取注册定制化URL.
     *
     * @param string $registerCode
     *
     * @return string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function getRegisterUri(string $registerCode = '')
    {
        if (!$registerCode) {
            /** @var array $response */
            $response = $this->detectAndCastResponseToType($this->getRegisterCode(), 'array');

            $registerCode = $response['register_code'];
        }

        $params = ['register_code' => $registerCode];

        return 'https://open.work.weixin.qq.com/3rdservice/wework/register?'.http_build_query($params);
    }

    /**
     * 获取注册码.
     *
     * @param string $corpName
     * @param string $adminName
     * @param string $adminMobile
     * @param string $state
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getRegisterCode(
        string $corpName = '',
        string $adminName = '',
        string $adminMobile = '',
        string $state = ''
    ) {
        $params = [];
        $params['template_id'] = $this->app['config']['reg_template_id'];
        !empty($corpName) && $params['corp_name'] = $corpName;
        !empty($adminName) && $params['admin_name'] = $adminName;
        !empty($adminMobile) && $params['admin_mobile'] = $adminMobile;
        !empty($state) && $params['state'] = $state;

        return $this->httpPostJson('cgi-bin/service/get_register_code', $params);
    }

    /**
     * 查询注册状态.
     *
     * Desc:该API用于查询企业注册状态，企业注册成功返回注册信息.
     *
     * @param string $registerCode
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getRegisterInfo(string $registerCode)
    {
        $params = [
            'register_code' => $registerCode,
        ];

        return $this->httpPostJson('cgi-bin/service/get_register_info', $params);
    }

    /**
     * 设置授权应用可见范围.
     *
     * Desc:调用该接口前提是开启通讯录迁移，收到授权成功通知后可调用。
     *      企业注册初始化安装应用后，应用默认可见范围为根部门。
     *      如需修改应用可见范围，服务商可以调用该接口设置授权应用的可见范围。
     *      该接口只能使用注册完成回调事件或者查询注册状态返回的access_token。
     *      调用设置通讯录同步完成后或者access_token超过30分钟失效（即解除通讯录锁定状态）则不能继续调用该接口。
     *
     * @param string $accessToken
     * @param string $agentId
     * @param array  $allowUser
     * @param array  $allowParty
     * @param array  $allowTag
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function setAgentScope(
        string $accessToken,
        string $agentId,
        array $allowUser = [],
        array $allowParty = [],
        array $allowTag = []
    ) {
        $params = [
            'agentid' => $agentId,
            'allow_user' => $allowUser,
            'allow_party' => $allowParty,
            'allow_tag' => $allowTag,
            'access_token' => $accessToken,
        ];

        return $this->httpGet('cgi-bin/agent/set_scope', $params);
    }

    /**
     * 设置通讯录同步完成.
     *
     * Desc:该API用于设置通讯录同步完成，解除通讯录锁定状态，同时使通讯录迁移access_token失效。
     *
     * @param string $accessToken
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function contactSyncSuccess(string $accessToken)
    {
        $params = ['access_token' => $accessToken];

        return $this->httpGet('cgi-bin/sync/contact_sync_success', $params);
    }
}
