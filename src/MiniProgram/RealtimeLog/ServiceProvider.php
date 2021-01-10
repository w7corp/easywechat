<?php

declare(strict_types=1);

namespace EasyWeChat\MiniProgram\RealtimeLog;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $app)
    {
        $app['realtime_log'] = function ($app) {
            return new Client($app);
        };
    }
}
