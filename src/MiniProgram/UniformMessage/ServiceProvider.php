<?php

declare(strict_types=1);

namespace EasyWeChat\MiniProgram\UniformMessage;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['uniform_message'] = function ($app) {
            return new Client($app);
        };
    }
}
