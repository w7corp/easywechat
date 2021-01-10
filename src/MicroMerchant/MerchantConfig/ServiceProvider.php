<?php

declare(strict_types=1);

namespace EasyWeChat\MicroMerchant\MerchantConfig;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['merchantConfig'] = function ($app) {
            return new Client($app);
        };
    }
}
