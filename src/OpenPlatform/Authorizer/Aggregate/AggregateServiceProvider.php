<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform\Authorizer\Aggregate;

use EasyWeChat\OpenPlatform\Authorizer\Aggregate\Account\Client;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class AggregateServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        !isset($app['account']) && $app['account'] = function ($app) {
            return new Client($app);
        };
    }
}
