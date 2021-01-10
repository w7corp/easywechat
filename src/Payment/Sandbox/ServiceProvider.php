<?php

declare(strict_types=1);

namespace EasyWeChat\Payment\Sandbox;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Pimple\Container $app
     */
    public function register(Container $app)
    {
        $app['sandbox'] = function ($app) {
            return new Client($app);
        };
    }
}
