<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\OpenPlatform\Authorizer;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $container)
    {
        $container['open_platform.authorizer_access_token'] = function ($container) {
            $accessToken = new AccessToken(
                $container['config']['open_platform']['app_id']
            );
            $accessToken->setApi($container['open_platform.api'])->setCache($container['cache']);

            return $accessToken;
        };

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
}
