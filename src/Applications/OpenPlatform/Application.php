<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\OpenPlatform;

use EasyWeChat\Applications\OfficialAccount\Application as OfficialAccount;
use EasyWeChat\Applications\OpenPlatform;
use EasyWeChat\Kernel\ServiceContainer;

/**
 * Class Application.
 *
 * @property \EasyWeChat\Applications\OpenPlatform\Server\Guard $server
 * @property \EasyWeChat\Applications\OpenPlatform\Auth\AccessToken $access_token
 * @property \EasyWeChat\Applications\OpenPlatform\PreAuthorization\Client $pre_authorization
 *
 * @method mixed getAuthorizationInfo(string $authCode = null)
 * @method mixed getAuthorizerInfo(string $appId)
 * @method mixed getAuthorizerOption(string $appId, string $name)
 * @method mixed setAuthorizerOption(string $appId, string $name, string $value)
 * @method mixed getAuthorizerList(int $offset = 0, int $count = 500)
 */
class Application extends ServiceContainer
{
    protected $providers = [
        OpenPlatform\Auth\ServiceProvider::class,
        OpenPlatform\Base\ServiceProvider::class,
        OpenPlatform\Server\ServiceProvider::class,
    ];

    protected $defaultConfig = [
        'http' => [
            'timeout' => 5.0,
            'base_uri' => 'https://api.weixin.qq.com/cgi-bin/component/',
        ],
    ];

    /**
     * Create an instance of OfficialAccount.
     *
     * @param string $appId
     * @param string $refreshToken
     *
     * @return \EasyWeChat\Applications\OfficialAccount\Application
     */
    public function createOfficialAccount(string $appId, string $refreshToken): OfficialAccount
    {
        $config = array_merge($this['config']->all(), [
            'component_app_id' => $this['config']['app_id'],
            'app_id' => $appId,
            'secret' => null,
            'refresh_token' => $refreshToken,
        ]);

        return (new OfficialAccount($config))->register(new Authorizer\ServiceProvider($this));
    }

    /**
     * Quick access to the base-api.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this['base'], $method], $args);
    }
}
