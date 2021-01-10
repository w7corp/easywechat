<?php

declare(strict_types=1);

namespace EasyWeChat\Work\OA;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['oa'] = function ($app) {
            return new Client($app);
        };
    }
}
