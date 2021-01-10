<?php

declare(strict_types=1);

namespace EasyWeChat\Payment\Refund;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['refund'] = function ($app) {
            return new Client($app);
        };
    }
}
