<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MiniProgram\QrCode;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * QrCode ServiceProvider.
 *
 * @author dysodeng <dysodengs@gmail.com>
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $pimple)
    {
        $pimple['qr_code'] = function ($app) {
            return new Client($app);
        };
    }
}
