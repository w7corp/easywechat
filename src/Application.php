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
use EasyWeChat\Support\Log;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;

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
     * Initialize logger.
     */
    private function initializeLogger()
    {
        if (!$this['config']['debug']) {
            $logger = new Logger('easywechat');
            $logger->pushHandler(new NullHandler());
            Log::setLogger($logger);
        } elseif ($logFile = $this['config']['log_file']) {
            $logger = new Logger('easywechat');
            $logger->pushHandler(new StreamHandler($logFile, $this['config']->get('log_level', Logger::WARNING)));
            Log::setLogger($logger);
        }
    }
}
