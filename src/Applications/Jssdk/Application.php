<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\Jssdk;

use EasyWeChat\Kernel\ServiceContainer;

/**
 * Class Application.
 *
 * @author overtrue <i@overtrue.me>
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $defaultConfig = [
        // http://docs.guzzlephp.org/en/stable/request-options.html
        'http' => [
            'timeout' => 5.0,
            'base_uri' => 'https://api.weixin.qq.com/',
        ],
    ];

    /**
     * Register Jssdk API client.
     */
    protected function afterRegistered()
    {
        $this['client'] = function ($app) {
            return new Client($app);
        };
    }

    /**
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return $this['client'](...$args);
    }
}
