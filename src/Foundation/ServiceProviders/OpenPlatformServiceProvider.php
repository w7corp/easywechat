<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * OpenPlatformServiceProvider.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @author    lixiao <leonlx126@gmail.com>
 * @copyright 2016
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Foundation\ServiceProviders;

use EasyWeChat\Encryption\Encryptor;
use EasyWeChat\Foundation\Application;
use EasyWeChat\OpenPlatform\AccessToken;
use EasyWeChat\OpenPlatform\Api\BaseApi;
use EasyWeChat\OpenPlatform\Api\PreAuthorization;
use EasyWeChat\OpenPlatform\Authorizer;
use EasyWeChat\OpenPlatform\AuthorizerAccessToken;
use EasyWeChat\OpenPlatform\EventHandlers;
use EasyWeChat\OpenPlatform\Guard;
use EasyWeChat\OpenPlatform\OpenPlatform;
use EasyWeChat\OpenPlatform\VerifyTicket;
use Overtrue\Socialite\SocialiteManager as Socialite;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class OpenPlatformServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple['open_platform.verify_ticket'] = function ($pimple) {
            return new VerifyTicket(
                $pimple['config']['open_platform']['app_id'],
                $pimple['cache']
            );
        };

        $pimple['open_platform.access_token'] = function ($pimple) {
            $accessToken = new AccessToken(
                $pimple['config']['open_platform']['app_id'],
                $pimple['config']['open_platform']['secret'],
                $pimple['cache']
            );
            $accessToken->setVerifyTicket($pimple['open_platform.verify_ticket']);

            return $accessToken;
        };

        $pimple['open_platform.encryptor'] = function ($pimple) {
            return new Encryptor(
                $pimple['config']['open_platform']['app_id'],
                $pimple['config']['open_platform']['token'],
                $pimple['config']['open_platform']['aes_key']
            );
        };

        $pimple['open_platform'] = function ($pimple) {
            return new OpenPlatform($pimple);
        };

        $pimple['open_platform.server'] = function ($pimple) {
            $server = new Guard($pimple['config']['open_platform']['token']);
            $server->debug($pimple['config']['debug']);
            $server->setEncryptor($pimple['open_platform.encryptor']);
            $server->setHandlers([
                Guard::EVENT_AUTHORIZED => $pimple['open_platform.handlers.authorized'],
                Guard::EVENT_UNAUTHORIZED => $pimple['open_platform.handlers.unauthorized'],
                Guard::EVENT_UPDATE_AUTHORIZED => $pimple['open_platform.handlers.updateauthorized'],
                Guard::EVENT_COMPONENT_VERIFY_TICKET => $pimple['open_platform.handlers.component_verify_ticket'],
            ]);

            return $server;
        };

        $pimple['open_platform.pre_auth'] = $pimple['open_platform.pre_authorization'] = function ($pimple) {
            return new PreAuthorization(
                $pimple['open_platform.access_token'],
                $pimple['request']
            );
        };

        $pimple['open_platform.api'] = function ($pimple) {
            return new BaseApi(
                $pimple['open_platform.access_token'],
                $pimple['request']
            );
        };

        $pimple['open_platform.authorizer'] = function ($pimple) {
            return new Authorizer(
                $pimple['open_platform.api'],
                $pimple['config']['open_platform']['app_id'],
                $pimple['cache']
            );
        };

        $pimple['open_platform.authorizer_access_token'] = function ($pimple) {
            return new AuthorizerAccessToken(
                $pimple['config']['open_platform']['app_id'],
                $pimple['open_platform.authorizer']
            );
        };

        // Authorization events handlers.
        $pimple['open_platform.handlers.component_verify_ticket'] = function ($pimple) {
            return new EventHandlers\ComponentVerifyTicket($pimple['open_platform.verify_ticket']);
        };
        $pimple['open_platform.handlers.authorized'] = function () {
            return new EventHandlers\Authorized();
        };
        $pimple['open_platform.handlers.updateauthorized'] = function () {
            return new EventHandlers\UpdateAuthorized();
        };
        $pimple['open_platform.handlers.unauthorized'] = function () {
            return new EventHandlers\Unauthorized();
        };

        $pimple['open_platform.app'] = function ($pimple) {
            return new Application($pimple['config']->toArray());
        };

        // OAuth for OpenPlatform.
        $pimple['open_platform.oauth'] = function ($pimple) {
            $callback = $this->prepareCallbackUrl($pimple);
            $scopes = $pimple['config']->get('open_platform.oauth.scopes', []);
            $config = [
                'wechat_open' => [
                    'client_id' => $pimple['open_platform.authorizer']->getAppId(),
                    'client_secret' => $pimple['open_platform.access_token'],
                    'redirect' => $callback,
                ],
            ];
            if ($pimple['config']->has('guzzle')) {
                $config['guzzle'] = $pimple['config']['guzzle'];
            }
            $socialite = (new Socialite($config))->driver('wechat_open');

            if (!empty($scopes)) {
                $socialite->scopes($scopes);
            }

            return $socialite;
        };
    }

    /**
     * Prepare the OAuth callback url for wechat.
     *
     * @param Container $pimple
     *
     * @return string
     */
    private function prepareCallbackUrl($pimple)
    {
        $callback = $pimple['config']->get('oauth.callback');
        if (0 === stripos($callback, 'http')) {
            return $callback;
        }
        $baseUrl = $pimple['request']->getSchemeAndHttpHost();

        return $baseUrl.'/'.ltrim($callback, '/');
    }
}
