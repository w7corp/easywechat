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

class Client extends BaseClient
{

    /**
     * Client constructor.
     * @param ServiceContainer $app
     */
    public function __construct(ServiceContainer $app)
    {
        parent::__construct($app, $app['provider_access_token']);
    }

    /**
     * 单点登录 - 获取登录的地址
     * @param string $redirect_uri
     * @param string $usertype
     * @param string $state
     * @return string
     */
    public function getLoginUrl(string $redirect_uri, string $usertype = 'admin', string $state = '')
    {
        $params = [
            'appid'        => $this->app['config']['corp_id'],
            'redirect_uri' => $redirect_uri,
            'usertype'     => $usertype,
            'state'        => $state || rand()
        ];
        return 'https://open.work.weixin.qq.com/wwopen/sso/3rd_qrConnect?' . http_build_query($params);
    }

    /**
     * 单点登录 - 获取登录用户信息
     * @param string $auth_code
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getLoginInfo(string $auth_code)
    {
        $params = [
            'auth_code' => $auth_code
        ];
        return $this->httpPostJson('cgi-bin/service/get_login_info', $params);
    }


    /**
     * 获取注册定制化URL
     * @param $register_code
     * @return string
     */
    public function getRegisterUri($register_code)
    {
        $params = ['register_code' => $register_code];
        return 'https://open.work.weixin.qq.com/3rdservice/wework/register?' . http_build_query($params);
    }


    /**
     * 获取注册码
     * @param string $corp_name
     * @param string $admin_name
     * @param string $admin_mobile
     * @param string $state
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getRegisterCode(
        string $corp_name = '',
        string $admin_name = '',
        string $admin_mobile = '',
        string $state = ''
    ) {
        $params = array();
        $params['template_id'] = $this->app['config']['template_id'];
        !empty($corp_name) && $params['corp_name'] = $corp_name;
        !empty($admin_name) && $params['admin_name'] = $admin_name;
        !empty($admin_mobile) && $params['admin_mobile'] = $admin_mobile;
        !empty($state) && $params['state'] = $state;
        return $this->httpPostJson('cgi-bin/service/get_register_code', $params);
    }

    /**
     * 查询注册状态
     * Desc:该API用于查询企业注册状态，企业注册成功返回注册信息。
     * @param string $register_code
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getRegisterInfo(string $register_code)
    {
        $params = [
            'register_code' => $register_code
        ];
        return $this->httpPostJson('cgi-bin/service/get_register_info', $params);
    }

    /**
     * 设置授权应用可见范围
     * Desc:调用该接口前提是开启通讯录迁移，收到授权成功通知后可调用。
     *      企业注册初始化安装应用后，应用默认可见范围为根部门。
     *      如需修改应用可见范围，服务商可以调用该接口设置授权应用的可见范围。
     *      该接口只能使用注册完成回调事件或者查询注册状态返回的access_token。
     *      调用设置通讯录同步完成后或者access_token超过30分钟失效（即解除通讯录锁定状态）则不能继续调用该接口。
     * @param string $access_token
     * @param string $agentid
     * @param array $allow_user
     * @param array $allow_party
     * @param array $allow_tag
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function setAgentScope(
        string $access_token,
        string $agentid,
        array $allow_user = [],
        array $allow_party = [],
        array $allow_tag = []
    ) {
        $this->middlewares = ['access_token' => $access_token];
        $params = [
            "agentid"     => $agentid,
            "allow_user"  => $allow_user,
            "allow_party" => $allow_party,
            "allow_tag"   => $allow_tag
        ];
        return $this->httpGet('cgi-bin/agent/set_scope', $params);
    }

    /**
     * 设置通讯录同步完成
     * Desc:该API用于设置通讯录同步完成，解除通讯录锁定状态，同时使通讯录迁移access_token失效。
     * @param $access_token
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function contactSyncSuccess(string $access_token)
    {
        $this->middlewares = ['access_token' => $access_token];
        return $this->httpGet('cgi-bin/sync/contact_sync_success');
    }


}