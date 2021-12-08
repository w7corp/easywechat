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
            return new Client($app);
        };

        $app['contact_way'] = function ($app) {
            return new ContactWayClient($app);
        };

        $app['external_contact_statistics'] = function ($app) {
            return new StatisticsClient($app);
        };

        $app['external_contact_message'] = function ($app) {
            return new MessageClient($app);
        };

        $app['school'] = function ($app) {
            return new SchoolClient($app);
        };
    }
}
