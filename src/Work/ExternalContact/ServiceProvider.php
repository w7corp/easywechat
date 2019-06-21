<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\ExternalContact;

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
        $app['external_contact'] = function ($app) {
            return new ExternalContact($app);
        };

        $app['external_contact.client'] = function ($app) {
            return new Client($app);
        };

        $app['external_contact.contact_way'] = function ($app) {
            return new ContactWayClient($app);
        };

        $app['external_contact.data_cube'] = function ($app) {
            return new DataCubeClient($app);
        };

        $app['external_contact.dimission'] = function ($app) {
            return new DimissionClient($app);
        };

        $app['external_contact.msg'] = function ($app) {
            return new MessageClient($app);
        };
    }
}
