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
 * FundamentalServiceProvider.php.
 *
 * This file is part of the wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Foundation\ServiceProviders;

use EasyWeChat\Fundamental\API;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class FundamentalServiceProvider.
 */
class FundamentalServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $pimple)
    {
        $pimple['fundamental.api'] = function ($pimple) {
            return new API($pimple['access_token']);
        };
    }
}
