<?php

declare(strict_types=1);

namespace EasyWeChat\Payment\Security;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['security'] = function ($app) {
            return new Client($app);
        };
    }
}
