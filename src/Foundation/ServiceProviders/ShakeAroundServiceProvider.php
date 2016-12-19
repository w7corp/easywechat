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
 * UserServiceProvider.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    allen05ren <allen05ren@outlook.com>
 * @copyright 2016 overtrue <i@overtrue.me>
 *
 * @see       https://github.com/overtrue/wechat
 * @see       http://overtrue.me
 */

namespace EasyWeChat\Foundation\ServiceProviders;

use EasyWeChat\ShakeAround\ShakeAround;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ShakeAroundServiceProvider.
 */
class ShakeAroundServiceProvider implements ServiceProviderInterface
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
        $pimple['shakearound'] = function ($pimple) {
            return new ShakeAround($pimple['access_token']);
        };
    }
}
