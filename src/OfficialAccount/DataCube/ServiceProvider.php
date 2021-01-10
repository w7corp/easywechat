<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\DataCube;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['data_cube'] = function ($app) {
            return new Client($app);
        };
    }
}
