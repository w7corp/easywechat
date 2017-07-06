<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\MiniProgram;

use EasyWeChat\Applications\BaseService\Application as BaseService;
use EasyWeChat\Kernel\ServiceContainer;

/**
 * Class Application.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [
        Auth\ServiceProvider::class,
        CustomerService\ServiceProvider::class,
        DataCube\ServiceProvider::class,
        AppCode\ServiceProvider::class,
        Sns\ServiceProvider::class,
        Server\ServiceProvider::class,
        TemplateMessage\ServiceProvider::class,
    ];

    public function afterRegistered()
    {
        $this['media'] = function () {
            return (new BaseService($this['config']->toArray()))->media;
        };
    }
}
