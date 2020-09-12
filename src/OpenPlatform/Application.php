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
use EasyWeChat\MiniProgram\Encryptor;
use EasyWeChat\OpenPlatform\Authorizer\Auth\AccessToken;
use EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application as MiniProgram;
use EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Auth\Client;
use EasyWeChat\OpenPlatform\Authorizer\OfficialAccount\Account\Client as AccountClient;
use EasyWeChat\OpenPlatform\Authorizer\OfficialAccount\Application as OfficialAccount;
use EasyWeChat\OpenPlatform\Authorizer\Server\Guard;

/**
 * Class Application.
 *
 * @property \EasyWeChat\OpenPlatform\Server\Guard        $server
 * @property \EasyWeChat\OpenPlatform\Auth\AccessToken    $access_token
 * @property \EasyWeChat\OpenPlatform\CodeTemplate\Client $code_template
 * @property \EasyWeChat\OpenPlatform\Component\Client    $component
 *
 * @method mixed handleAuthorize(string $authCode = null)
 * @method mixed getAuthorizer(string $appId)
 * @method mixed getAuthorizerOption(string $appId, string $name)
 * @method mixed setAuthorizerOption(string $appId, string $name, string $value)
 * @method mixed getAuthorizers(int $offset = 0, int $count = 500)
 * @method mixed createPreAuthorizationCode()
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
        CodeTemplate\ServiceProvider::class,
        Component\ServiceProvider::class,
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
     * @param string                                                    $appId
     * @param string|null                                               $refreshToken
     * @param \EasyWeChat\OpenPlatform\Authorizer\Auth\AccessToken|null $accessToken
     *
     * @return \EasyWeChat\OpenPlatform\Authorizer\OfficialAccount\Application
     */
    public function officialAccount(string $appId, string $refreshToken = null, AccessToken $accessToken = null): OfficialAccount
    {
        $application = new OfficialAccount($this->getAuthorizerConfig($appId, $refreshToken), $this->getReplaceServices($accessToken) + [
            'encryptor' => $this['encryptor'],

            'account' => function ($app) {
                return new AccountClient($app, $this);
            },
        ]);

        $application->extend('oauth', function ($socialite) {
            /* @var \Overtrue\Socialite\Providers\WeChat $socialite */
            return $socialite;
        });

        return $application;
    }

    /**
     * Creates the miniProgram application.
     *
     * @param string                                                    $appId
     * @param string|null                                               $refreshToken
     * @param \EasyWeChat\OpenPlatform\Authorizer\Auth\AccessToken|null $accessToken
     *
     * @return \EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Application
     */
    public function miniProgram(string $appId, string $refreshToken = null, AccessToken $accessToken = null): MiniProgram
    {
        return new MiniProgram($this->getAuthorizerConfig($appId, $refreshToken), $this->getReplaceServices($accessToken) + [
            'encryptor' => function () {
                return new Encryptor($this['config']['app_id'], $this['config']['token'], $this['config']['aes_key']);
            },

            'auth' => function ($app) {
                return new Client($app, $this);
            },
        ]);
    }

    /**
     * Return the pre-authorization login page url.
     *
     * @param string            $callbackUrl
     * @param string|array|null $optional
     *
     * @return string
     */
    public function getPreAuthorizationUrl(string $callbackUrl, $optional = []): string
    {
        // 兼容旧版 API 设计
        if (\is_string($optional)) {
            $optional = [
                'pre_auth_code' => $optional,
            ];
        } else {
            $optional['pre_auth_code'] = $this->createPreAuthorizationCode()['pre_auth_code'];
        }

        $queries = \array_merge($optional, [
            'component_appid' => $this['config']['app_id'],
            'redirect_uri' => $callbackUrl,
        ]);

        return 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?'.http_build_query($queries);
    }

    /**
     * Return the pre-authorization login page url (mobile).
     *
     * @param string            $callbackUrl
     * @param string|array|null $optional
     *
     * @return string
     */
    public function getMobilePreAuthorizationUrl(string $callbackUrl, $optional = []): string
    {
        // 兼容旧版 API 设计
        if (\is_string($optional)) {
            $optional = [
                'pre_auth_code' => $optional,
            ];
        } else {
            $optional['pre_auth_code'] = $this->createPreAuthorizationCode()['pre_auth_code'];
        }

        $queries = \array_merge($optional, [
            'component_appid' => $this['config']['app_id'],
            'redirect_uri' => $callbackUrl,
            'action' => 'bindcomponent',
            'no_scan' => 1,
        ]);

        return 'https://mp.weixin.qq.com/safe/bindcomponent?'.http_build_query($queries).'#wechat_redirect';
    }

    /**
     * @param string      $appId
     * @param string|null $refreshToken
     *
     * @return array
     */
    protected function getAuthorizerConfig(string $appId, string $refreshToken = null): array
    {
        return $this['config']->merge([
            'component_app_id' => $this['config']['app_id'],
            'component_app_token' => $this['config']['token'],
            'app_id' => $appId,
            'refresh_token' => $refreshToken,
        ])->toArray();
    }

    /**
     * @param \EasyWeChat\OpenPlatform\Authorizer\Auth\AccessToken|null $accessToken
     *
     * @return array
     */
    protected function getReplaceServices(AccessToken $accessToken = null): array
    {
        $services = [
            'access_token' => $accessToken ?: function ($app) {
                return new AccessToken($app, $this);
            },

            'server' => function ($app) {
                return new Guard($app);
            },
        ];

        foreach (['cache', 'http_client', 'log', 'logger', 'request'] as $reuse) {
            if (isset($this[$reuse])) {
                $services[$reuse] = $this[$reuse];
            }
        }

        return $services;
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
        return $this->base->$method(...$args);
    }
}
