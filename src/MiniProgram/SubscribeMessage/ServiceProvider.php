<?php

declare(strict_types=1);

namespace EasyWeChat\MiniProgram\SubscribeMessage;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['subscribe_message'] = function ($app) {
            return new Client($app);
        };
    }
}
