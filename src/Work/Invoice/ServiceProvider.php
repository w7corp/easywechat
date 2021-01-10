<?php

declare(strict_types=1);

namespace EasyWeChat\Work\Invoice;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['invoice'] = function ($app) {
            return new Client($app);
        };
    }
}
