<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Kernel\Providers;

use EasyWeChatComposer\Extension;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ExtensionServiceProvider.
 *
 * @author overtrue <i@overtrue.me>
 */
class ExtensionServiceProvider implements ServiceProviderInterface
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
        !isset($pimple['extension']) && $pimple['extension'] = function ($app) {
            return new Extension($app);
        };
    }
}
