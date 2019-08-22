<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\WiFi;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 *
 * @author her-cat <i@her-cat.com>
 */
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
