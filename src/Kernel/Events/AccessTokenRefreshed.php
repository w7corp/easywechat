<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Kernel\Events;

use EasyWeChat\Kernel\AccessToken;

/**
 * Class AccessTokenRefreshed.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class AccessTokenRefreshed
{
    /**
     * @var \EasyWeChat\Kernel\AccessToken
     */
    public $accessToken;

    public function __construct(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;
    }
}
