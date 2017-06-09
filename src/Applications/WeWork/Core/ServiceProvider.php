<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\WeWork\Core;

use EasyWeChat\Applications\WeWork\Application;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $container)
    {
        $container['we_work.instance'] = function ($container) {
            return new Application($container);
        };

        $container['we_work.access_token'] = function ($container) {
            return new AccessToken($container['config']['corp_id']);
        };
    }
}
