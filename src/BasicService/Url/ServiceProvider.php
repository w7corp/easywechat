<?php

declare(strict_types=1);

namespace EasyWeChat\BasicService\Url;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['url'] = function ($app) {
            return new Client($app);
        };
    }
}
