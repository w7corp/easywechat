<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat;

use EasyWeChat\Work\AgentFactory;

/**
 * Class Factory.
 *
 * @method static \EasyWeChat\Payment\Application            payment(array $config)
 * @method static \EasyWeChat\MiniProgram\Application        miniProgram(array $config)
 * @method static \EasyWeChat\OpenPlatform\Application       openPlatform(array $config)
 * @method static \EasyWeChat\OfficialAccount\Application    officialAccount(array $config)
 * @method static \EasyWeChat\BasicService\Application       basicService(array $config)
 */
class Factory
{
    /**
     * @param string $name
     * @param array  $config
     *
     * @return \EasyWeChat\Kernel\ServiceContainer|\EasyWeChat\Work\AgentFactory
     */
    public static function make($name, array $config)
    {
        $namespace = Kernel\Support\Str::studly($name);
        $application = "\\EasyWeChat\\{$namespace}\\Application";

        if ('Work' === $namespace) {
            return self::work($config);
        }

        return new $application($config);
    }

    /**
     * @param array $config
     *
     * @return \EasyWeChat\Work\AgentFactory
     */
    public static function work(array $config)
    {
        return new AgentFactory($config);
    }

    /**
     * Dynamically pass methods to the application.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return self::make($name, ...$arguments);
    }
}
