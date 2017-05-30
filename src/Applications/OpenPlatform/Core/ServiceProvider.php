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
 * @copyright 2017
 *
 * @see      https://github.com/overtrue/wechat
 * @see      http://overtrue.me
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
        $container['open_platform.verify_ticket'] = function ($container) {
            return new VerifyTicket(
                $container['config']['open_platform']['app_id'],
                $container['cache']
            );
        };

        $container['open_platform.access_token'] = function ($container) {
            $accessToken = new AccessToken(
                $container['config']['open_platform']['app_id'],
                $container['config']['open_platform']['secret']
            );
            $accessToken->setCache($container['cache'])
                        ->setVerifyTicket($container['open_platform.verify_ticket']);

            return $accessToken;
        };

        $container['open_platform.authorizer_access_token'] = function ($container) {
            $accessToken = new AuthorizerAccessToken(
                $container['config']['open_platform']['app_id']
            );
            $accessToken->setApi($container['open_platform.api'])->setCache($container['cache']);

            return $accessToken;
        };
    }
}
