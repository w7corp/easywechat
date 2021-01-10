<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork\MiniProgram;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        !isset($app['mini_program']) && $app['mini_program'] = function ($app) {
            return new Client($app);
        };
    }
}
