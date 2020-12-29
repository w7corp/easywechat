<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform\Auth;

use EasyWeChat\Kernel\AccessToken as BaseAccessToken;

/**
 * Class AccessToken.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class AccessToken extends BaseAccessToken
{
    /**
     * @var string
     */
    protected $requestMethod = 'POST';

    /**
     * @var string
     */
    protected $tokenKey = 'component_access_token';

    /**
     * @var string
     */
    protected $endpointToGetToken = 'cgi-bin/component/api_component_token';

    /**
     * @return array
     */
    protected function getCredentials(): array
    {
        return [
            'component_appid' => $this->app['config']['app_id'],
            'component_appsecret' => $this->app['config']['secret'],
            'component_verify_ticket' => $this->app['verify_ticket']->getTicket(),
        ];
    }
}
