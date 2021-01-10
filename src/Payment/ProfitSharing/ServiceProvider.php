<?php

declare(strict_types=1);

namespace EasyWeChat\Payment\ProfitSharing;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['profit_sharing'] = function ($app) {
            return new Client($app);
        };
    }
}
