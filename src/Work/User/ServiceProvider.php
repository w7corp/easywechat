<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\User;

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
        $app['user'] = function ($app) {
            return new Client($app);
        };

        $app['tag'] = function ($app) {
            return new TagClient($app);
        };

        $app['linked_corp'] = function ($app) {
            return new LinkedCorpClient($app);
        };

        $app['batch_jobs'] = function ($app) {
            return new BatchJobsClient($app);
        };
    }
}
