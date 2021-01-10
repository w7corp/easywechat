<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork\Auth;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * ServiceProvider.
 *
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        isset($app['provider_access_token']) || $app['provider_access_token'] = function ($app) {
            return new AccessToken($app);
        };
    }
}
