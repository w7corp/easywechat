<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform\Auth;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['verify_ticket'] = function ($app) {
            return new VerifyTicket($app);
        };

        $app['access_token'] = function ($app) {
            return new AccessToken($app);
        };
    }
}
