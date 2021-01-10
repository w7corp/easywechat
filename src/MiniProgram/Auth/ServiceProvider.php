<?php

declare(strict_types=1);

namespace EasyWeChat\MiniProgram\Auth;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        !isset($app['access_token']) && $app['access_token'] = function ($app) {
            return new AccessToken($app);
        };

        !isset($app['auth']) && $app['auth'] = function ($app) {
            return new Client($app);
        };
    }
}
