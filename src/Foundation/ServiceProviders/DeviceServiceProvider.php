<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * QRCodeServiceProvider.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    soone <66812590@qq.com>
 * @copyright 2016
 *
 * @see      https://github.com/overtrue/wechat
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Foundation\ServiceProviders;

use EasyWeChat\Device\Device;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class DeviceServiceProvider.
 */
class DeviceServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple['device'] = function ($pimple) {
            return new Device($pimple['access_token'], $pimple['config']->get('device', []));
        };
    }
}
