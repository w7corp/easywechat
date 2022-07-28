<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenWork\License;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * ServiceProvider.
 *
 * @author moniang <me@imoniang.com>
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        !isset($app['license_order']) && $app['license_order'] = function ($app) {
            return new Client($app);
        };

        !isset($app['license_account']) && $app['license_account'] = function ($app) {
            return new Account($app);
        };

        !isset($app['license_app']) && $app['license_app'] = function ($app) {
            return new App($app);
        };

        !isset($app['license_auto_active']) && $app['license_auto_active'] = function ($app) {
            return new AutoActive($app);
        };
    }
}
