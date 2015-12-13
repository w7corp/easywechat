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
use ErrorException;
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
     * The exception handler.
     *
     * @var callable
     */
    protected $exceptionHandler;

    /**
     * Application constructor.
     *
     * @param array $config
     */
    public function __construct($config)
    {
        parent::__construct();

        $this['config'] = function () use ($config) {
            return new Config($config);
        };

        $this->registerProviders();
        $this->registerBase();
        $this->registerExceptionHandler();
        $this->initializeLogger();

        Log::info('Current configuration:', $config);
    }

    /**
     * Set the exception handler.
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function setExceptionHanler(callable $callback)
    {
        $this->exceptionHandler = $callback;

        return $this;
    }

    /**
     * Return current exception handler.
     *
     * @return callable
     */
    public function getExceptionHandler()
    {
        return $this->exceptionHandler;
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
        $this['request'] = function () {
            return Request::createFromGlobals();
        };

        $this['cache'] = function () {
            return new CacheManager();
        };

        $this['access_token'] = function () {
           return new AccessToken(
               $this['config']['app_id'],
               $this['config']['secret'],
               $this['cache']
           );
        };
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

            $this->exceptionHandler && call_user_func_array($this->exceptionHandler, [$e]);

            if (is_callable($lastExceptionHandler)) {
                return call_user_func($lastExceptionHandler, $e);
            }
        });

        $errorHandler = function ($severity, $message, $file, $line) use ($logTemplate) {
            Log::error(sprintf($logTemplate, $severity, $message, $file, $line));

            if (error_reporting() & $severity) {
                throw new ErrorException($message, 0, $severity, $file, $line);
            }
        };

        set_error_handler($errorHandler);

        register_shutdown_function(function () use ($logTemplate, $errorHandler) {
            $lastError = error_get_last();

            if ($lastError['type'] === E_ERROR) {
                // fatal error
                $errorHandler(E_ERROR, $lastError['message'], $lastError['file'], $lastError['line']);
            }
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
