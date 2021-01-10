<?php

declare(strict_types=1);

namespace EasyWeChat\Payment\Redpack;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['redpack'] = function ($app) {
            return new Client($app);
        };
    }
}
