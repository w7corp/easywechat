<?php

/*
 * This file is part of the overtrue/wechat.
 *
 */

namespace EasyWeChat\MiniProgram\Business;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 *
 * @author wangdongzhao <elim051@163.com>
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['business'] = function ($app) {
            return new Client($app);
        };
    }
}
