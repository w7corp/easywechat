<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform;

use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\MiniProgram\Application as MiniProgram;
use EasyWeChat\OfficialAccount\Application as OfficialAccount;

/**
 * Class Application.
 *
 * @property \EasyWeChat\OpenPlatform\Server\Guard     $server
 * @property \EasyWeChat\OpenPlatform\Auth\AccessToken $access_token
 *
 * @method mixed getAuthorizationInfo(string $authCode = null)
 * @method mixed getAuthorizerInfo(string $appId)
 * @method mixed getAuthorizerOption(string $appId, string $name)
 * @method mixed setAuthorizerOption(string $appId, string $name, string $value)
 * @method mixed getAuthorizerList(int $offset = 0, int $count = 500)
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [
        Auth\ServiceProvider::class,
        Base\ServiceProvider::class,
        Server\ServiceProvider::class,
    ];

    /**
     * @var array
     */
    protected $defaultConfig = [
        'http' => [
            'timeout' => 5.0,
            'base_uri' => 'https://api.weixin.qq.com/cgi-bin/component/',
        ],
    ];

    /**
     * Creates the officialAccount application.
     *
     * @param string $appId
     * @param string $refreshToken
     *
     * @return \EasyWeChat\OfficialAccount\Application
     */
    public function officialAccount(string $appId, string $refreshToken): OfficialAccount
    {
        return (new OfficialAccount($this->authorizerConfig($appId, $refreshToken)))->register(new Authorizer\ServiceProvider($this));
    }

    /**
     * Creates the miniProgram application.
     *
     * @param string $appId
     * @param string $refreshToken
     *
     * @return \EasyWeChat\MiniProgram\Application
     */
    public function miniProgram(string $appId, string $refreshToken): MiniProgram
    {
        return (new MiniProgram($this->authorizerConfig($appId, $refreshToken)))->register(new Authorizer\ServiceProvider($this));
    }

    /**
     * @param string $appId
     * @param string $refreshToken
     *
     * @return array
     */
    protected function authorizerConfig(string $appId, string $refreshToken): array
    {
        return [
            'app_id' => $appId,
            'refresh_token' => $refreshToken,
        ];
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
