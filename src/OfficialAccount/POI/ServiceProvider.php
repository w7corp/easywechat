<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\POI;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['poi'] = function ($app) {
            return new Client($app);
        };
    }
}
