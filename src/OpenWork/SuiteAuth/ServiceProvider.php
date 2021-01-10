<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork\SuiteAuth;

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
        $app['suite_ticket'] = function ($app) {
            return new SuiteTicket($app);
        };

        isset($app['suite_access_token']) || $app['suite_access_token'] = function ($app) {
            return new AccessToken($app);
        };
    }
}
