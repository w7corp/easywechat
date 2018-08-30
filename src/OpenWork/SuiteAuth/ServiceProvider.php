<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenWork\SuiteAuth;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * ServiceProvider.
 *
 * @author xiaomin <keacefull@gmail.com>
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['suite_ticket'] = function ($app) {
            return new SuiteTicket($app);
        };

        isset($app['suite_access_token']) || $app['suite_access_token'] = function ($app) {
            return new AccessToken($app);
        };
    }
}
