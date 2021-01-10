<?php

declare(strict_types=1);

namespace EasyWeChat\Payment\Coupon;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['coupon'] = function ($app) {
            return new Client($app);
        };
    }
}
