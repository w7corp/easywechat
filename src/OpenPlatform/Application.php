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
use EasyWeChat\OpenPlatform\Authorizer\Aggregate\AggregateServiceProvider;
use EasyWeChat\OpenPlatform\Authorizer\MiniProgram\MiniProgramServiceProvider;
use EasyWeChat\OpenPlatform\Authorizer\Server\Guard;
use EasyWeChat\OpenPlatform\OAuth\ComponentDelegate;

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
            'base_uri' => 'https://api.weixin.qq.com/',
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
        $application = new OfficialAccount($this->getAuthorizerConfig($appId, $refreshToken), $this->getReplaceServices($accessToken));

        $application->extend('oauth', function ($socialite) {
            /* @var \Overtrue\Socialite\Providers\WeChatProvider $socialite */
            return $socialite->component(new ComponentDelegate($this));
        });

        return $application->register(new AggregateServiceProvider());
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
        $application = new MiniProgram($this->getAuthorizerConfig($appId, $refreshToken), $this->getReplaceServices($accessToken));

        $application->register(new MiniProgramServiceProvider());

        return $application;
    }

    /**
     * Return the pre-authorization login page url.
     *
     * @param string $callbackUrl
     *
     * @return string
     */
    public function getPreAuthorizationUrl(string $callbackUrl): string
    {
        $queries = [
            'component_appid' => $this['config']['app_id'],
            'pre_auth_code' => $this->createPreAuthorizationCode()['pre_auth_code'],
            'redirect_uri' => $callbackUrl,
        ];

        return 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?'.http_build_query($queries);
    }

    /**
     * @param string $appId
     * @param string $refreshToken
     *
     * @return array
     */
    protected function getAuthorizerConfig(string $appId, string $refreshToken): array
    {
        return [
            'debug' => $this['config']->get('debug', false),
            'response_type' => $this['config']->get('response_type', 'array'),
            'log' => $this['config']->get('log', []),
            'app_id' => $appId,
            'refresh_token' => $refreshToken,
        ];
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

            'server' => function ($app) {
                return new Guard($app);
            },

            'encryptor' => $this['encryptor'],
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
