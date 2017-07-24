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
use EasyWeChat\OpenPlatform\Server\Guard;
use EasyWeChat\OpenPlatform\Server\Handlers;

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
     * @param string                                              $appId
     * @param string                                              $refreshToken
     * @param \EasyWeChat\OpenPlatform\Auth\AuthorizerAccessToken $accessToken
     *
     * @return OfficialAccount
     */
    public function officialAccount(string $appId, string $refreshToken, AuthorizerAccessToken $accessToken): OfficialAccount
    {
        return new OfficialAccount([
            'app_id' => $appId,
            'refresh_token' => $refreshToken,
        ], $this->getReplaceServices($accessToken));
    }

    /**
     * Creates the miniProgram application.
     *
     * @param string                                              $appId
     * @param string                                              $refreshToken
     * @param \EasyWeChat\OpenPlatform\Auth\AuthorizerAccessToken $accessToken
     *
     * @return MiniProgram
     */
    public function miniProgram(string $appId, string $refreshToken, AuthorizerAccessToken $accessToken): MiniProgram
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
    public function getRedirectUrl(string $callbackUrl)
    {
        return self::COMPONENT_LOGIN_PAGE.'?'.http_build_query([
                'component_appid' => $this['config']['app_id'],
                'pre_auth_code' => $this->createPreAuthorizationCode()['pre_auth_code'],
                'redirect_uri' => $callbackUrl,
            ]);
    }

    /**
     * @param AuthorizerAccessToken $accessToken
     *
     * @return array
     */
    protected function getReplaceServices(AuthorizerAccessToken $accessToken)
    {
        return [
            'access_token' => $accessToken ?? function ($app) {
                $accessToken = new AuthorizerAccessToken($app);
                $accessToken->setOpenPlatformAccessToken($this['access_token']);

                return $accessToken;
            },

            'encryptor' => function () {
                return new Encryptor(
                    $this['config']['app_id'],
                    $this['config']['token'],
                    $this['config']['aes_key']
                );
            },

            'server' => function ($app) {
                $server = (new Guard($app))->debug($app['config']['debug']);
                $handlers = [
                    Guard::EVENT_AUTHORIZED => new Handlers\Authorized($app),
                    Guard::EVENT_UNAUTHORIZED => new Handlers\Unauthorized($app),
                    Guard::EVENT_UPDATE_AUTHORIZED => new Handlers\UpdateAuthorized($app),
                    Guard::EVENT_COMPONENT_VERIFY_TICKET => new Handlers\VerifyTicketRefreshed($app),
                ];

                return $server->setHandlers($handlers);
            },
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
