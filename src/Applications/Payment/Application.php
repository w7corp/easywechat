<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\Payment;

use EasyWeChat\Kernel\ServiceContainer;

class Application extends ServiceContainer
{
    protected $providers = [
        Pay\ServiceProvider::class,
        Tools\ServiceProvider::class,
    ];

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this['pay'], $name], $arguments);
    }
}
