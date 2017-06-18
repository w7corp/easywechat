<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\Payment\Pay;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 *
 * @author overtrue <i@overtrue.me>
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['merchant'] = function ($app) {
            return new Merchant($app['config']->all());
        };

        $app['pay'] = function ($app) {
            $client = new Client($app);
            $client->sandboxMode(
                (bool) $app['config']->get('sandbox_mode')
            );

            return $client;
        };
    }
}
