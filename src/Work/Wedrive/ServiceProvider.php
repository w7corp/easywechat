<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\Wedrive;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider
 *
 * @author lio990527 <lio990527@163.com>
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $app)
    {
        $app['wedrive'] = function ($app) {
            return new Wedrive([
                'base' => new Client($app),
                'space' => new SpaceClient($app),
                'file' => new FileClient($app),
            ]);
        };
    }
}
