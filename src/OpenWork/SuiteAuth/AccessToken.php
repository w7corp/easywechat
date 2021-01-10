<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork\SuiteAuth;

use  EasyWeChat\Kernel\AccessToken as BaseAccessToken;

/**
 * AccessToken.
 *
 */
class AccessToken extends BaseAccessToken
{
    /**
     * @var string
     */
    protected string  $requestMethod = 'POST';

    /**
     * @var string
     */
    protected string  $endpointToGetToken = 'cgi-bin/service/get_suite_token';

    /**
     * @var string
     */
    protected string  $tokenKey = 'suite_access_token';

    /**
     * @var string
     */
    protected string  $cachePrefix = 'easywechat.kernel.suite_access_token.';

    /**
     * Credential for get token.
     *
     * @return array
     */
    protected function getCredentials(): array
    {
        return [
            'suite_id' => $this->app['config']['suite_id'],
            'suite_secret' => $this->app['config']['suite_secret'],
            'suite_ticket' => $this->app['suite_ticket']->getTicket(),
        ];
    }
}
