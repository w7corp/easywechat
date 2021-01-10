<?php

declare(strict_types=1);

namespace EasyWeChat\Payment\Transfer;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['transfer'] = function ($app) {
            return new Client($app);
        };
    }
}
