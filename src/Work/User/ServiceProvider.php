<?php

declare(strict_types=1);

namespace EasyWeChat\Work\User;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['user'] = function ($app) {
            return new Client($app);
        };

        $app['tag'] = function ($app) {
            return new TagClient($app);
        };
    }
}
