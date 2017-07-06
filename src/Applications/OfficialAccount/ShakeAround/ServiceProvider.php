<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\OfficialAccount\ShakeAround;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 *
 * @author allen05ren <allen05ren@outlook.com>
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['shakearound'] = function ($container) {
            return new Client($container);
        };

        $app['shakearound_device'] = function ($container) {
            return new DeviceClient($container);
        };

        $app['shakearound_page'] = function ($container) {
            return new PageClient($container);
        };

        $app['shakearound_group'] = function ($container) {
            return new GroupClient($container);
        };

        $app['shakearound_stats'] = function ($container) {
            return new DeviceClient($container);
        };
    }
}
