<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform\Authorizer\MiniProgram;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MiniProgramServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['code'] = function ($app) {
            return new Code\Client($app);
        };

        $app['domain'] = function ($app) {
            return new Domain\Client($app);
        };
    }
}
