<?php

declare(strict_types=1);

namespace EasyWeChat\MiniProgram\Broadcast;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['broadcast'] = function ($app) {
            return new Client($app);
        };
    }
}
