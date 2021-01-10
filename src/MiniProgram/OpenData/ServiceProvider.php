<?php

declare(strict_types=1);

namespace EasyWeChat\MiniProgram\OpenData;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['open_data'] = function ($app) {
            return new Client($app);
        };
    }
}
