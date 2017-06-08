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
 * ServiceProvider.php.
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

namespace EasyWeChat\Applications\OpenPlatform;

use EasyWeChat\Application;
use EasyWeChat\Applications\OpenPlatform\Api\BaseApi;
use EasyWeChat\Applications\OpenPlatform\Api\PreAuthorization;
use Overtrue\Socialite\SocialiteManager as Socialite;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $container)
    {
        $container['open_platform.instance'] = function ($container) {
            return new OpenPlatform($container);
        };

        $container['open_platform.pre_auth'] = function ($container) {
            return new PreAuthorization(
                $container['open_platform.access_token'],
                $container['request']
            );
        };

        $container['open_platform.api'] = function ($container) {
            return new BaseApi(
                $container['open_platform.access_token'],
                $container['request']
            );
        };

        // Authorization events handlers.
        $container['open_platform.handlers.component_verify_ticket'] = function ($container) {
            return new EventHandlers\ComponentVerifyTicket($container['open_platform.verify_ticket']);
        };
        $container['open_platform.handlers.authorized'] = function () {
            return new EventHandlers\Authorized();
        };
        $container['open_platform.handlers.updateauthorized'] = function () {
            return new EventHandlers\UpdateAuthorized();
        };
        $container['open_platform.handlers.unauthorized'] = function () {
            return new EventHandlers\Unauthorized();
        };

        $container['open_platform.app'] = function ($container) {
            return new Application($container['config']->toArray());
        };

        // OAuth for OpenPlatform.
        $container['open_platform.oauth'] = function ($container) {
            $callback = $this->prepareCallbackUrl($container);
            $scopes = $container['config']->get('open_platform.oauth.scopes', []);
            $socialite = (new Socialite([
                'wechat_open' => [
                    'client_id' => $container['open_platform.authorizer_access_token']->getAppId(),
                    'client_secret' => $container['open_platform.access_token'],
                    'redirect' => $callback,
                ],
            ]))->driver('wechat_open');

            if (!empty($scopes)) {
                $socialite->scopes($scopes);
            }

            return $socialite;
        };
    }

    /**
     * Prepare the OAuth callback url for wechat.
     *
     * @param Container $container
     *
     * @return string
     */
    private function prepareCallbackUrl($container)
    {
        $callback = $container['config']->get('oauth.callback');
        if (0 === stripos($callback, 'http')) {
            return $callback;
        }
        $baseUrl = $container['request']->getSchemeAndHttpHost();

        return $baseUrl.'/'.ltrim($callback, '/');
    }
}
