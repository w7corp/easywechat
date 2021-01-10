<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\ShakeAround;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
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
