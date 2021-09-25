<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\Kf;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 *
 * @package EasyWeChat\Work\Kf
 *
 * @author 读心印 <aa24615@qq.com>
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['kf_account'] = function ($app) {
            return new AccountClient($app);
        };

        $app['kf_servicer'] = function ($app) {
            return new ServicerClient($app);
        };

        $app['kf_message'] = function ($app) {
            return new MessageClient($app);
        };
    }
}
