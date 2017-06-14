<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\WeWork;

use EasyWeChat\Applications\WeWork;
use EasyWeChat\Support\ServiceContainer;

/**
 * Application.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Application extends ServiceContainer
{
    protected $providers = [
        WeWork\OA\ServiceProvider::class,
        WeWork\Core\ServiceProvider::class,
        WeWork\Menu\ServiceProvider::class,
        WeWork\User\ServiceProvider::class,
        WeWork\Agent\ServiceProvider::class,
        WeWork\Message\ServiceProvider::class,
        WeWork\Department\ServiceProvider::class,
    ];
}
