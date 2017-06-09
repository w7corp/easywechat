<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\MiniProgram\Core;

use EasyWeChat\Applications\Base\Core\AccessToken as BaseAccessToken;

/**
 * Class AccessToken.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class AccessToken extends BaseAccessToken
{
    /**
     * {@inheritdoc}.
     */
    protected $prefix = 'easywechat.common.mini.program.access_token.';

    const API_TOKEN_GET = 'https://api.weixin.qq.com/cgi-bin/token';
}
