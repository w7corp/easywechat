<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork\Auth;

use  EasyWeChat\Kernel\AccessToken as BaseAccessToken;

/**
 * AccessToken.
 *
 */
class AccessToken extends BaseAccessToken
{
    protected $requestMethod = 'POST';

    /**
     * @var string
     */
    protected string  $endpointToGetToken = 'cgi-bin/service/get_provider_token';

    /**
     * @var string
     */
    protected string  $tokenKey = 'provider_access_token';

    /**
     * @var string
     */
    protected string  $cachePrefix = 'easywechat.kernel.provider_access_token.';

    /**
     * Credential for get token.
     *
     * @return array
     */
    protected function getCredentials(): array
    {
        return [
            'corpid' => $this->app['config']['corp_id'], //服务商的corpid
            'provider_secret' => $this->app['config']['secret'],
        ];
    }
}
