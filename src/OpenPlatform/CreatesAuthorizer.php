<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform;

/**
 * Trait CreatesAuthorizer.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
trait CreatesAuthorizer
{
    /**
     * Creates the authorizer application.
     *
     * @param string $application
     * @param string $appId
     * @param string $refreshToken
     *
     * @return mixed
     */
    protected function createAuthorizer($application, string $appId, string $refreshToken)
    {
        $config = [
            'open_platform' => [
                'app_id' => $this['config']['app_id'],
                'token' => $this['config']['token'],
                'aes_key' => $this['config']['aes_key'],
            ],
            'app_id' => $appId,
            'refresh_token' => $refreshToken,
        ];

        return (new $application($config))->register(new Authorizer\ServiceProvider($this));
    }
}
