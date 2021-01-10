<?php

declare(strict_types=1);

namespace EasyWeChat\BasicService\QrCode;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['qrcode'] = function ($app) {
            return new Client($app);
        };
    }
}
