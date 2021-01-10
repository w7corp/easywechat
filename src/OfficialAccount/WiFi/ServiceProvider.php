<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\WiFi;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $app)
    {
        $app['wifi'] = function ($app) {
            return new Client($app);
        };

        $app['wifi_card'] = function ($app) {
            return new CardClient($app);
        };

        $app['wifi_device'] = function ($app) {
            return new DeviceClient($app);
        };

        $app['wifi_shop'] = function ($app) {
            return new ShopClient($app);
        };
    }
}
