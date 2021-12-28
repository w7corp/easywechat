<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\GroupWelcomeTemplate;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * 入群欢迎语素材管理
 *
 * @package EasyWeChat\Work\GroupWelcomeTemplate\ServiceProvider
 * @author HaoLiang <haoliang@qiyuankeji.cn>
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['group_welcome_template'] = function ($app) {
            return new Client($app);
        };
    }
}
