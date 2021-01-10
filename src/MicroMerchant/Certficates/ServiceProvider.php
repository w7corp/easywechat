<?php

declare(strict_types=1);

namespace EasyWeChat\MicroMerchant\Certficates;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['certficates'] = function ($app) {
            return new Client($app);
        };
    }
}
