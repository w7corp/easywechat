<?php

declare(strict_types=1);

namespace EasyWeChat\MiniProgram\Live;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['live'] = function ($app) {
            return new Client($app);
        };
    }
}
