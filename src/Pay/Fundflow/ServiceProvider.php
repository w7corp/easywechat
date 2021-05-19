<?php

declare(strict_types=1);

namespace EasyWeChat\Pay\Fundflow;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['fundflow'] = function ($app) {
            return new Client($app);
        };
    }
}
