<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform\Auth;

use EasyWeChat\Kernel\AccessToken as BaseAccessToken;

class AccessToken extends BaseAccessToken
{
    /**
     * @var string
     */
    protected string  $requestMethod = 'POST';

    /**
     * @var string
     */
    protected string  $tokenKey = 'component_access_token';

    /**
     * @var string
     */
    protected string  $endpointToGetToken = 'cgi-bin/component/api_component_token';

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
