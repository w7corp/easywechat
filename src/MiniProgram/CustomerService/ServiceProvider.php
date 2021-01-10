<?php

declare(strict_types=1);

namespace EasyWeChat\MiniProgram\CustomerService;

use EasyWeChat\OfficialAccount\CustomerService\Client;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['customer_service'] = function ($app) {
            return new Client($app);
        };
    }
}
