<?php
/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\MiniProgram;

use EasyWeChat\Kernel\ServiceContainer;

/**
 * Class Application.
 *
 * @author Caikeal <caikeal@qq.com>
 *
 * @property \EasyWeChat\Work\MiniProgram\Auth\Client $auth
 */
class Application extends ServiceContainer
{
    /**
     * Application constructor.
     *
     * @param array $config
     * @param array $prepends
     */
    public function __construct(array $config = [], array $prepends = [])
    {
        parent::__construct($config, $prepends);

        $providers = [
            Auth\ServiceProvider::class,
        ];

        foreach ($providers as $provider) {
            $this->register(new $provider());
        }
    }
}
