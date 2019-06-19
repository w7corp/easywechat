<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\Crm;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['crm'] = function ($app) {
            return new Crm($app);
        };

        $app['crm.client'] = function ($app) {
            return new Client($app);
        };

        $app['crm.contact_way'] = function ($app) {
            return new ContactWayClient($app);
        };

        $app['crm.data_cube'] = function ($app) {
            return new DataCubeClient($app);
        };

        $app['crm.dimission'] = function ($app) {
            return new DimissionClient($app);
        };

        $app['crm.msg'] = function ($app) {
            return new MessageClient($app);
        };
    }
}
