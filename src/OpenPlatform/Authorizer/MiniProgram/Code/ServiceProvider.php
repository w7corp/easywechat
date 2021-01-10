<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Code;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['code'] = function ($app) {
            return new Client($app);
        };
    }
}
