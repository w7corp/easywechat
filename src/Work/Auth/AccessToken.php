<?php

declare(strict_types=1);

namespace EasyWeChat\Work\Auth;

use EasyWeChat\Kernel\AccessToken as BaseAccessToken;

class AccessToken extends BaseAccessToken
{
    /**
     * @var string
     */
    protected string  $endpointToGetToken = 'cgi-bin/gettoken';

    /**
     * @var int
     */
    protected $safeSeconds = 0;

    /**
     * Credential for get token.
     *
     * @return array
     */
    protected function getCredentials(): array
    {
        return [
            'corpid' => $this->app['config']['corp_id'],
            'corpsecret' => $this->app['config']['secret'],
        ];
    }
}
