<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\OpenPlatform\Authorizer;

use EasyWeChat\Applications\OpenPlatform\Application;
use EasyWeChat\Applications\OpenPlatform\Encryption\Encryptor;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * OpenPlatform instance.
     *
     * @var \EasyWeChat\Applications\OpenPlatform\Application
     */
    protected $application;

    /**
     * ServiceProvider constructor.
     *
     * @param \EasyWeChat\Applications\OpenPlatform\Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['access_token'] = function ($app) {
            $accessToken = new AccessToken($app);
            $accessToken->setOpenPlatformAccessToken($this->application['access_token']);

            return $accessToken;
        };

        $app['encryptor'] = function ($app) {
            return new Encryptor(
                $app['config']['component_app_id'],
                $app['config']['token'],
                $app['config']['aes_key']
            );
        };
    }
}
