<?php

declare(strict_types=1);

namespace EasyWeChat\Work\Calendar;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $app)
    {
        $app['calendar'] = function ($app) {
            return new Client($app);
        };
    }
}
