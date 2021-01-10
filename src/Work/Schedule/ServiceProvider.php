<?php

declare(strict_types=1);

namespace EasyWeChat\Work\Schedule;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $app)
    {
        $app['schedule'] = function ($app) {
            return new Client($app);
        };
    }
}
