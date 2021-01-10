<?php

declare(strict_types=1);

namespace EasyWeChat\Work\Media;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['media'] = function ($app) {
            return new Client($app);
        };
    }
}
