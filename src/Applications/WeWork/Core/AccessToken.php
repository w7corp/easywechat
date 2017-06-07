<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * AccessToken.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @copyright 2017
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Applications\WeWork\Core;

use EasyWeChat\Applications\Base\Core\AccessToken as BaseAccessToken;

class AccessToken extends BaseAccessToken
{
    /**
     * {@inheritdoc}.
     */
    protected $prefix = 'easywechat.common.enterprise.access_token.';

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
        return $this->prefix.$this->clientId.$this->clientSecret;
    }
}
