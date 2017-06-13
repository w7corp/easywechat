<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\OpenPlatform\Core;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $container)
    {
        $container['verify_ticket'] = function ($container) {
            return new VerifyTicket(
                $container['config']['open_platform']['app_id'],
                $container['cache']
            );
        };

        $container['access_token'] = function ($container) {
            $accessToken = new AccessToken(
                $container['config']['open_platform']['app_id'],
                $container['config']['open_platform']['secret']
            );
            $accessToken->setCache($container['cache'])->setVerifyTicket($container['verify_ticket']);

            return $accessToken;
        };
    }
}
