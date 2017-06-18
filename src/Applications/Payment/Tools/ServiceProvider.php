<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\Payment\Tools;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['coupon'] = function ($app) {
            return new Coupon\Client($app);
        };

        $app['redpack'] = function ($app) {
            return new Redpack\Client($app);
        };

        $app['transfer'] = function ($app) {
            return new Transfer\Client($app);
        };
    }
}
