<?php

declare(strict_types=1);

namespace EasyWeChat\MiniProgram\ActivityMessage;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['activity_message'] = function ($app) {
            return new Client($app);
        };
    }
}
