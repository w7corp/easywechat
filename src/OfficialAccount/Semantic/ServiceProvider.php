<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Semantic;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['semantic'] = function ($app) {
            return new Client($app);
        };
    }
}
