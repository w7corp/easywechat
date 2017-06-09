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
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Applications\OfficialAccount\Core;

use EasyWeChat\Applications\OfficialAccount\Application;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $container)
    {
        $container['official_account.instance'] = function ($container) {
            return new Application($container);
        };

        $container['official_account.access_token'] = function ($container) {
            $accessToken = new AccessToken(
                $container['config']['app_id'],
                $container['config']['secret']
            );

            $accessToken->setCache($container['cache']);

            return $accessToken;
        };
    }
}
