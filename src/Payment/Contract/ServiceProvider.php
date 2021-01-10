<?php

declare(strict_types=1);

namespace EasyWeChat\Payment\Contract;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['contract'] = function ($app) {
            return new Client($app);
        };
    }
}
