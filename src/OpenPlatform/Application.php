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
use EasyWeChat\OpenPlatform\Auth\AuthorizerAccessToken;
use EasyWeChat\OpenPlatform\Authorizer\Account\Client;

/**
 * Class Application.
 *
 * @property \EasyWeChat\OpenPlatform\Server\Guard     $server
 * @property \EasyWeChat\OpenPlatform\Auth\AccessToken $access_token
 *
 * @method mixed handleAuthorize(string $authCode = null)
 * @method mixed getAuthorizer(string $appId)
 * @method mixed getAuthorizerOption(string $appId, string $name)
 * @method mixed setAuthorizerOption(string $appId, string $name, string $value)
 * @method mixed getAuthorizers(int $offset = 0, int $count = 500)
 * @method \EasyWeChat\Kernel\Support\Collection createPreAuthorizationCode()
 */
class Application extends ServiceContainer
{
    const COMPONENT_LOGIN_PAGE = 'https://mp.weixin.qq.com/cgi-bin/componentloginpage';

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
     * @param string                                                   $appId
     * @param string                                                   $refreshToken
     * @param \EasyWeChat\OpenPlatform\Auth\AuthorizerAccessToken|null $accessToken
     *
     * @return \EasyWeChat\OfficialAccount\Application
     */
    public function officialAccount(string $appId, string $refreshToken, AuthorizerAccessToken $accessToken = null): OfficialAccount
    {
        return new OfficialAccount([
            'app_id' => $appId,
            'refresh_token' => $refreshToken,
        ], $this->getReplaceServices($accessToken));
    }

    /**
     * Creates the miniProgram application.
     *
     * @param string                                                   $appId
     * @param string                                                   $refreshToken
     * @param \EasyWeChat\OpenPlatform\Auth\AuthorizerAccessToken|null $accessToken
     *
     * @return \EasyWeChat\MiniProgram\Application
     */
    public function miniProgram(string $appId, string $refreshToken, AuthorizerAccessToken $accessToken = null): MiniProgram
    {
        return new MiniProgram([
            'app_id' => $appId,
            'refresh_token' => $refreshToken,
        ], $this->getReplaceServices($accessToken));
    }

    /**
     * Return the url to redirect to authorization page.
     *
     * @param string $callbackUrl
     *
     * @return string
     */
    public function getRedirectUrl(string $callbackUrl): string
    {
        return self::COMPONENT_LOGIN_PAGE.'?'.http_build_query([
                'component_appid' => $this['config']['app_id'],
                'pre_auth_code' => $this->createPreAuthorizationCode()['pre_auth_code'],
                'redirect_uri' => $callbackUrl,
            ]);
    }

    /**
     * @param \EasyWeChat\OpenPlatform\Auth\AuthorizerAccessToken|null $accessToken
     *
     * @return array
     */
    protected function getReplaceServices(AuthorizerAccessToken $accessToken = null): array
    {
        return [
            'access_token' => $accessToken ?? function ($app) {
                return new AuthorizerAccessToken($app, $this);
            },

            'encryptor' => $this['encryptor'],

            'account' => function ($app) {
                return new Client($app);
            },
        ];
    }

    /**
     * Handle dynamic calls.
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
