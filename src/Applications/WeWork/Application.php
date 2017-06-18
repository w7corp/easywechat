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
use EasyWeChat\Kernel\ServiceContainer;

/**
 * Application.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Application extends ServiceContainer
{
    protected $providers = [
        WeWork\OA\ServiceProvider::class,
        WeWork\Auth\ServiceProvider::class,
        WeWork\Menu\ServiceProvider::class,
        WeWork\User\ServiceProvider::class,
        WeWork\Agent\ServiceProvider::class,
        WeWork\Media\ServiceProvider::class,
        WeWork\Message\ServiceProvider::class,
        WeWork\Department\ServiceProvider::class,
    ];

    /**
     * @var array
     */
    protected $defaultConfig = [
        // http://docs.guzzlephp.org/en/stable/request-options.html
        'http' => [
            'timeout' => 5.0,
            'base_uri' => 'https://qyapi.weixin.qq.com/cgi-bin/',
        ],
    ];
}
