<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\ShakeAround;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 *
 * @author allen05ren <allen05ren@outlook.com>
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['shake_around'] = function ($app) {
            return new ShakeAround($app);
        };

        $app['shake_around.device'] = function ($app) {
            return new DeviceClient($app);
        };

        $app['shake_around.page'] = function ($app) {
            return new PageClient($app);
        };

        $app['shake_around.material'] = function ($app) {
            return new MaterialClient($app);
        };

        $app['shake_around.group'] = function ($app) {
            return new GroupClient($app);
        };

        $app['shake_around.relation'] = function ($app) {
            return new RelationClient($app);
        };

        $app['shake_around.stats'] = function ($app) {
            return new StatsClient($app);
        };
    }
}
