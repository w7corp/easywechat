<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\User;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['user'] = function ($app) {
            return new UserClient($app);
        };

        $app['user_tag'] = function ($app) {
            return new TagClient($app);
        };
    }
}
