<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform\CodeTemplate;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['code_template'] = function ($app) {
            return new Client($app);
        };
    }
}
