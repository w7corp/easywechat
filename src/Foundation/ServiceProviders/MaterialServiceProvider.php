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
 * MaterialServiceProvider.php.
 *
 * This file is part of the wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Foundation\ServiceProviders;

use EasyWeChat\Material\Material;
use EasyWeChat\Material\Temporary;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class MaterialServiceProvider.
 */
class MaterialServiceProvider implements ServiceProviderInterface
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
        $pimple['material'] = function ($pimple) {
            return new Material($pimple['access_token']);
        };

        $temporary = function ($pimple) {
            return new Temporary($pimple['access_token']);
        };

        $pimple['material_temporary'] = $temporary;
        $pimple['material.temporary'] = $temporary;
    }
}
