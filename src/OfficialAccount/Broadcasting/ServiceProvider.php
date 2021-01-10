<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Broadcasting;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['broadcasting'] = function ($app) {
            return new Client($app);
        };
    }
}
