<?php

declare(strict_types=1);

namespace EasyWeChat\Work\Department;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['department'] = function ($app) {
            return new Client($app);
        };
    }
}
