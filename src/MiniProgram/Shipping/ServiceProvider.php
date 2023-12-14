<?php

namespace EasyWeChat\MiniProgram\Shipping;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['shipping'] = function ($app) {
            return new Client($app);
        };
    }
}
