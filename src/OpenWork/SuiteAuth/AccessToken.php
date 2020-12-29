<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenWork\SuiteAuth;

use  EasyWeChat\Kernel\AccessToken as BaseAccessToken;

/**
 * AccessToken.
 *
 * @author xiaomin <keacefull@gmail.com>
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
    protected $endpointToGetToken = 'cgi-bin/service/get_suite_token';

    /**
     * @var string
     */
    protected $tokenKey = 'suite_access_token';

    /**
     * @var string
     */
    protected $cachePrefix = 'easywechat.kernel.suite_access_token.';

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
