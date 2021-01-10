<?php

declare(strict_types=1);

namespace EasyWeChat\MiniProgram\Base;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['base'] = function ($app) {
            return new Client($app);
        };
    }
}
