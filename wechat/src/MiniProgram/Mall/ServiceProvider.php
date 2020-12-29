<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MiniProgram\Mall;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['mall'] = function ($app) {
            return new ForwardsMall($app);
        };

        $app['mall.order'] = function ($app) {
            return new OrderClient($app);
        };

        $app['mall.cart'] = function ($app) {
            return new CartClient($app);
        };

        $app['mall.product'] = function ($app) {
            return new ProductClient($app);
        };

        $app['mall.media'] = function ($app) {
            return new MediaClient($app);
        };
    }
}
