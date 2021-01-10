<?php

declare(strict_types=1);

namespace EasyWeChat\MiniProgram\Soter;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $app)
    {
        $app['soter'] = function ($app) {
            return new Client($app);
        };
    }
}
