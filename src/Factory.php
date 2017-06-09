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

use EasyWeChat\Config\Repository as Config;
use EasyWeChat\Support\Log;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * @method static \EasyWeChat\Applications\WeWork\Application                    weWork(array $config)
 * @method static \EasyWeChat\Applications\MiniProgram\MiniProgram          miniProgram(array $config)
 * @method static \EasyWeChat\Applications\OpenPlatform\Application        openPlatform(array $config)
 * @method static \EasyWeChat\Applications\OfficialAccount\Application  officialAccount(array $config)
 */
class Factory extends Container
{
    /**
     * @param string                              $application
     * @param array|\EasyWeChat\Config\Repository $config
     *
     * @return \EasyWeChat\Support\ServiceContainer
     */
    public static function make($application, $config)
    {
        if (!($config instanceof Config)) {
            $config = new Config($config);
        }

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

    /**
     * Initialize logger.
     */
    private function initializeLogger()
    {
        if (Log::hasLogger()) {
            return;
        }

        $logger = new Logger('easywechat');

        if (!$this['config']['debug'] || defined('PHPUNIT_RUNNING')) {
            $logger->pushHandler(new NullHandler());
        } elseif ($this['config']['log.handler'] instanceof HandlerInterface) {
            $logger->pushHandler($this['config']['log.handler']);
        } elseif ($logFile = $this['config']['log.file']) {
            $logger->pushHandler(new StreamHandler(
                    $logFile,
                    $this['config']->get('log.level', Logger::WARNING),
                    true,
                    $this['config']->get('log.permission', null))
            );
        }

        Log::setLogger($logger);
    }
}
