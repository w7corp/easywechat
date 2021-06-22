<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\CorpGroup;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

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
        $app['corp_group'] = function ($app) {
            return new Client($app);
        };
    }
}
