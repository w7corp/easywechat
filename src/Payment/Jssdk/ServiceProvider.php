<?php

declare(strict_types=1);

namespace EasyWeChat\Payment\Jssdk;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['jssdk'] = function ($app) {
            return new Client($app);
        };
    }
}
