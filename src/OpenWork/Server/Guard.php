<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenWork\Server;

use EasyWeChat\Kernel\ServerGuard;

/**
 * Guard.
 *
 * @author xiaomin <keacefull@gmail.com>
 */
class Guard extends ServerGuard
{
    /**
     * @var bool
     */
    protected $alwaysValidate = true;

    /**
     * @return bool
     */
    public function validate()
    {
        return $this;
    }

    /**
     * @return bool
     */
    protected function shouldReturnRawResponse(): bool
    {
        return !is_null($this->app['request']->get('echostr'));
    }

    protected function isSafeMode(): bool
    {
        return true;
    }
}
