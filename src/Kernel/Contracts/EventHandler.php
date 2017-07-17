<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Kernel\Contracts;

/**
 * Class EventHandler.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
interface EventHandler
{
    /**
     * Handle an incoming event message from WeChat server-side.
     *
     * @param \EasyWeChat\Kernel\Support\Collection $message
     */
    public function handle($message);
}
