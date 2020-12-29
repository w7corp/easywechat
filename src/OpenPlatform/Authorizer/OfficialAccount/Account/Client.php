<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform\Authorizer\OfficialAccount\Account;

use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OpenPlatform\Application;
use EasyWeChat\OpenPlatform\Authorizer\Aggregate\Account\Client as BaseClient;

/**
 * Class Client.
 *
 * @author Keal <caiyuezhang@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * @var \EasyWeChat\OpenPlatform\Application
     */
    protected $component;

    /**
     * Client constructor.
     *
     * @param \EasyWeChat\Kernel\ServiceContainer  $app
     * @param \EasyWeChat\OpenPlatform\Application $component
     */
    public function __construct(ServiceContainer $app, Application $component)
    {
        parent::__construct($app);

        $this->component = $component;
    }

    /**
     * 从第三方平台跳转至微信公众平台授权注册页面, 授权注册小程序.
     *
     * @param string $callbackUrl
     * @param bool   $copyWxVerify
     *
     * @return string
     */
    public function getFastRegistrationUrl(string $callbackUrl, bool $copyWxVerify = true): string
    {
        $queries = [
            'copy_wx_verify' => $copyWxVerify,
            'component_appid' => $this->component['config']['app_id'],
            'appid' => $this->app['config']['app_id'],
            'redirect_uri' => $callbackUrl,
        ];

        return 'https://mp.weixin.qq.com/cgi-bin/fastregisterauth?'.http_build_query($queries);
    }

    /**
     * 小程序快速注册.
     *
     * @param string $ticket
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function register(string $ticket)
    {
        $params = [
            'ticket' => $ticket,
        ];

        return $this->httpPostJson('cgi-bin/account/fastregister', $params);
    }
}
