<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenWork\MiniProgram;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        !isset($app['mini_program']) && $app['mini_program'] = function ($app) {
            return new Client($app);
        };
    }
}
