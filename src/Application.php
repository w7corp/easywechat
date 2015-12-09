<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Application.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015
 *
 * @link      https://github.com/overtrue/wechat
 * @link      http://overtrue.me
 */

namespace Overtrue\WeChat;

use EasyWeChat\Cache\Manager as CacheManager;
use EasyWeChat\Core\AccessToken;
use EasyWeChat\Core\Exception as EasyWeChatException;
use EasyWeChat\Support\Log;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Application.
 */
class Application extends Container
{
    /**
     * Service Providers.
     *
     * @var array
     */
    protected $providers = [
        'Overtrue\\WeChat\\ServiceProviders\\ServerServiceProvider',
        'Overtrue\\WeChat\\ServiceProviders\\UserServiceProvider',
        'Overtrue\\WeChat\\ServiceProviders\\JsServiceProvider',
        'Overtrue\\WeChat\\ServiceProviders\\MenuServiceProvider',
        'Overtrue\\WeChat\\ServiceProviders\\NoticeServiceProvider',
        'Overtrue\\WeChat\\ServiceProviders\\MaterialServiceProvider',
        'Overtrue\\WeChat\\ServiceProviders\\StaffServiceProvider',
        'Overtrue\\WeChat\\ServiceProviders\\UrlServiceProvider',
        'Overtrue\\WeChat\\ServiceProviders\\QRCodeServiceProvider',
        'Overtrue\\WeChat\\ServiceProviders\\SemanticServiceProvider',
        'Overtrue\\WeChat\\ServiceProviders\\StatsServiceProvider',
    ];

    /**
     * Application constructor.
     *
     * @param array $config
     */
    public function __construct($config)
    {
        parent::__construct();

        $this['config'] = $this->factory(function () use ($config) {
            return new Config($config);
        });

        $this->registerProviders();
        $this->registerBase();
        $this->registerExceptionHandler();
        $this->initializeLogger();
    }

    /**
     * Register providers.
     */
    private function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }
    }

    /**
     * Register basic providers.
     */
    private function registerBase()
    {
        $this['request'] = $this->factory(function () {
            return Request::createFromGlobals();
        });

        $this['cache'] = $this->factory(function () {
            return new CacheManager();
        });

        $this['access_token'] = $this->factory(function () {
           return new AccessToken(
               $this['config']['app_id'],
               $this['config']['secret'],
               $this['cache']
           );
        });
    }

    /**
     * Register exception and error handler.
     */
    private function registerExceptionHandler()
    {
        $logTemplate          = '%s: %s in %s on line %s.';
        $lastExceptionHandler = set_exception_handler(function ($e) use (&$lastExceptionHandler, $logTemplate) {
            if ($e instanceof EasyWeChatException) {
                return Log::error(sprintf($logTemplate, $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine()));
            }

            if (is_callable($lastExceptionHandler)) {
                return call_user_func($lastExceptionHandler, $e);
            }
        });

        set_error_handler(function ($severity, $message, $file, $line) use ($logTemplate) {
            if (!(error_reporting() & $severity)) {
                return Log::error(sprintf($logTemplate, $severity, $message, $file, $line));
            }

            throw new ErrorException($message, 0, $severity, $file, $line);
        });
    }

    /**
     * Initialize logger.
     */
    private function initializeLogger()
    {
        $logger = new Logger('easywechat');

        if (!$this['config']['debug']) {
            $logger->pushHandler(new NullHandler());
        } elseif ($logFile = $this['config']['log.file']) {
            $logger->pushHandler(new StreamHandler($logFile, $this['config']->get('log.level', Logger::WARNING)));
        }

        Log::setLogger($logger);
    }
}
