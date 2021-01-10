<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Setting;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['setting'] = function ($app) {
            return new Client($app);
        };
    }
}
