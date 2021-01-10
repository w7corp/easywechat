<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform\Authorizer\OfficialAccount\MiniProgram;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['mini_program'] = function ($app) {
            return new Client($app);
        };
    }
}
