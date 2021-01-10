<?php

declare(strict_types=1);

namespace EasyWeChat\Work\Agent;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['agent'] = function ($app) {
            return new Client($app);
        };
    }
}
