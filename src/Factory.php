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

use EasyWeChat\Support\Str;

/**
 * Class Factory.
 *
 * @method static \EasyWeChat\Applications\WeWork\Application             weWork(array $config)
 * @method static \EasyWeChat\Applications\Payment\Application            payment(array $config)
 * @method static \EasyWeChat\Applications\MiniProgram\Application        miniProgram(array $config)
 * @method static \EasyWeChat\Applications\OpenPlatform\Application       openPlatform(array $config)
 * @method static \EasyWeChat\Applications\OfficialAccount\Application    officialAccount(array $config)
 */
class Factory
{
    /**
     * @param string $name
     * @param array  $config
     *
     * @return \EasyWeChat\Kernel\ServiceContainer
     */
    public static function make($name, array $config)
    {
        $namespace = Str::studly($name);
        $application = "\\EasyWeChat\\Applications\\{$namespace}\\Application";

        return new $application($config);
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
