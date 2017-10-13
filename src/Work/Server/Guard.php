<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\Server;

use EasyWeChat\Kernel\ServerGuard;

/**
 * Class Guard.
 *
 * @author overtrue <i@overtrue.me>
 */
class Guard extends ServerGuard
{
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
}
