<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Goods;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['goods'] = function ($app) {
            return new Client($app);
        };
    }
}
