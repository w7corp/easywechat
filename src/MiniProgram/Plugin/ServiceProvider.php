<?php

declare(strict_types=1);

namespace EasyWeChat\MiniProgram\Plugin;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param \Pimple\Container $app
     */
    public function register(Container $app)
    {
        $app['plugin'] = function ($app) {
            return new Client($app);
        };

        $app['plugin_dev'] = function ($app) {
            return new DevClient($app);
        };
    }
}
