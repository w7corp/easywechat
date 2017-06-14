<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\WeWork\Core;

use EasyWeChat\Applications\Base\AccessToken as BaseAccessToken;

class AccessToken extends BaseAccessToken
{
    /**
     * {@inheritdoc}.
     */
    protected $prefix = 'easywechat.wework.access_token.';

    // API
    const API_TOKEN_GET = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken';

    /**
     * {@inheritdoc}.
     */
    public function requestFields(): array
    {
        return [
            'corpid' => $this->clientId,
            'corpsecret' => $this->clientSecret,
        ];
    }

    /**
     * {@inheritdoc}.
     */
    public function getCacheKey(): string
    {
        return $this->prefix.md5($this->clientId.'easywechat.wework'.$this->clientSecret);
    }
}
