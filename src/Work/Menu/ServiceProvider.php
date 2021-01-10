<?php

declare(strict_types=1);

namespace EasyWeChat\Work\Menu;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['menu'] = function ($app) {
            return new Client($app);
        };
    }
}
