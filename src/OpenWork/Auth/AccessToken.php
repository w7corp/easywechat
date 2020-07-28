<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenWork\Auth;

use  EasyWeChat\Kernel\AccessToken as BaseAccessToken;

/**
 * AccessToken.
 *
 * @author xiaomin <keacefull@gmail.com>
 */
class AccessToken extends BaseAccessToken
{
    protected $requestMethod = 'POST';

    /**
     * @var string
     */
    protected $endpointToGetToken = 'cgi-bin/service/get_provider_token';

    /**
     * @var string
     */
    protected $tokenKey = 'provider_access_token';

    /**
     * @var string
     */
    protected $cachePrefix = 'easywechat.kernel.provider_access_token.';

    /**
     * Credential for get token.
     */
    protected function getCredentials(): array
    {
        return [
            'corpid' => $this->app['config']['corp_id'], //服务商的corpid
            'provider_secret' => $this->app['config']['secret'],
        ];
    }
}
