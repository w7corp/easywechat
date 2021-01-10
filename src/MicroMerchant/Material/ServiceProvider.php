<?php

declare(strict_types=1);

namespace EasyWeChat\MicroMerchant\Material;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['material'] = function ($app) {
            return new Client($app);
        };
    }
}
