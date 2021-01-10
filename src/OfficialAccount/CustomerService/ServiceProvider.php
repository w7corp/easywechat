<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\CustomerService;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['customer_service'] = function ($app) {
            return new Client($app);
        };

        $app['customer_service_session'] = function ($app) {
            return new SessionClient($app);
        };
    }
}
