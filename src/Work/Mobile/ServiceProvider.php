<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\Mobile;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use EasyWeChat\Work\Mobile\Auth\Client;

/**
 * ServiceProvider.
 *
 * @author 读心印 <aa24615@qq.com>
 */
class ServiceProvider implements ServiceProviderInterface
{
    protected $app;

    /**
     * @param Container $app
     */
    public function register(Container $app)
    {
        $app['mobile'] = function ($app) {
            return new Client($app);
        };
    }
}
