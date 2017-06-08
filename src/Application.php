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
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat;

use Doctrine\Common\Cache\Cache as CacheInterface;
use Doctrine\Common\Cache\FilesystemCache;
use EasyWeChat\Applications\Base\Core\Http;
use EasyWeChat\Config\Repository as Config;
use EasyWeChat\Exceptions\FaultException;
use EasyWeChat\Support\Log;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method static \EasyWeChat\Applications\WeWork\WeWork                    wework(array $config)
 * @method static \EasyWeChat\Applications\MiniProgram\MiniProgram          miniProgram(array $config)
 * @method static \EasyWeChat\Applications\OpenPlatform\OpenPlatform        openPlatform(array $config)
 * @method static \EasyWeChat\Applications\OfficialAccount\OfficialAccount  officialAccount(array $config)
 */
class Application extends Container
{
    /**
     * Service Providers.
     *
     * @var array
     */
    protected $providers = [
        /*
         * OfficialAccount Service Providers...
         */
        \EasyWeChat\Applications\OfficialAccount\Core\ServiceProvider::class,
        \EasyWeChat\Applications\OfficialAccount\Server\ServiceProvider::class,
        \EasyWeChat\Applications\OfficialAccount\User\ServiceProvider::class,
        \EasyWeChat\Applications\OfficialAccount\Js\ServiceProvider::class,
        \EasyWeChat\Applications\OfficialAccount\OAuth\ServiceProvider::class,
        \EasyWeChat\Applications\OfficialAccount\Menu\ServiceProvider::class,
        \EasyWeChat\Applications\OfficialAccount\TemplateMessage\ServiceProvider::class,
        \EasyWeChat\Applications\OfficialAccount\Material\ServiceProvider::class,
        \EasyWeChat\Applications\OfficialAccount\CustomerService\ServiceProvider::class,
        \EasyWeChat\Applications\OfficialAccount\Url\ServiceProvider::class,
        \EasyWeChat\Applications\OfficialAccount\QRCode\ServiceProvider::class,
        \EasyWeChat\Applications\OfficialAccount\Semantic\ServiceProvider::class,
        \EasyWeChat\Applications\OfficialAccount\Stats\ServiceProvider::class,
        \EasyWeChat\Applications\OfficialAccount\Payment\ServiceProvider::class,
        \EasyWeChat\Applications\OfficialAccount\POI\ServiceProvider::class,
        \EasyWeChat\Applications\OfficialAccount\Reply\ServiceProvider::class,
        \EasyWeChat\Applications\OfficialAccount\Broadcast\ServiceProvider::class,
        \EasyWeChat\Applications\OfficialAccount\Card\ServiceProvider::class,
        \EasyWeChat\Applications\OfficialAccount\Device\ServiceProvider::class,
        \EasyWeChat\Applications\OfficialAccount\ShakeAround\ServiceProvider::class,
        \EasyWeChat\Applications\OfficialAccount\Comment\ServiceProvider::class,

        /*
         * OpenPlatform Service Providers...
         */
        \EasyWeChat\Applications\OpenPlatform\ServiceProvider::class,
        \EasyWeChat\Applications\OpenPlatform\Core\ServiceProvider::class,
        \EasyWeChat\Applications\OpenPlatform\Server\ServiceProvider::class,

        /*
         * MiniProgram Service Providers...
         */
        \EasyWeChat\Applications\MiniProgram\ServiceProvider::class,
        \EasyWeChat\Applications\MiniProgram\Sns\ServiceProvider::class,
        \EasyWeChat\Applications\MiniProgram\Stats\ServiceProvider::class,
        \EasyWeChat\Applications\MiniProgram\QRCode\ServiceProvider::class,
        \EasyWeChat\Applications\MiniProgram\Server\ServiceProvider::class,
        \EasyWeChat\Applications\MiniProgram\Material\ServiceProvider::class,
        \EasyWeChat\Applications\MiniProgram\CustomerService\ServiceProvider::class,
        \EasyWeChat\Applications\MiniProgram\TemplateMessage\ServiceProvider::class,

        /*
         * WeWork Service Providers...
         */
        \EasyWeChat\Applications\WeWork\Core\ServiceProvider::class,
        \EasyWeChat\Applications\WeWork\Department\ServiceProvider::class,
        \EasyWeChat\Applications\WeWork\Attendance\ServiceProvider::class,
        \EasyWeChat\Applications\WeWork\Agent\ServiceProvider::class,
    ];

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

        if ($this['config']['debug']) {
            error_reporting(E_ALL);
        }

        $this->registerProviders();
        $this->registerBase();
        $this->initializeLogger();

        Http::setDefaultOptions($this['config']->get('guzzle', ['timeout' => 5.0]));

        $this->logConfiguration($config);
    }

    /**
     * Log configuration.
     *
     * @param array $config
     */
    public function logConfiguration($config)
    {
        $config = new Config($config);

        $keys = ['app_id', 'secret', 'open_platform.app_id', 'open_platform.secret', 'mini_program.app_id', 'mini_program.secret'];
        foreach ($keys as $key) {
            !$config->has($key) || $config[$key] = '***'.substr($config[$key], -5);
        }

        Log::debug('Current config:', $config->toArray());
    }

    /**
     * Add a provider.
     *
     * @param string $provider
     *
     * @return Application
     */
    public function addProvider($provider)
    {
        array_push($this->providers, $provider);

        return $this;
    }

    /**
     * Set providers.
     *
     * @param array $providers
     */
    public function setProviders(array $providers)
    {
        $this->providers = [];

        foreach ($providers as $provider) {
            $this->addProvider($provider);
        }
    }

    /**
     * Return all providers.
     *
     * @return array
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * Magic get access.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function __get($id)
    {
        return $this->offsetGet($id);
    }

    /**
     * Magic set access.
     *
     * @param string $id
     * @param mixed  $value
     */
    public function __set($id, $value)
    {
        $this->offsetSet($id, $value);
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

        if (!empty($this['config']['cache']) && $this['config']['cache'] instanceof CacheInterface) {
            $this['cache'] = $this['config']['cache'];
        } else {
            $this['cache'] = function () {
                return new FilesystemCache(sys_get_temp_dir());
            };
        }
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

    /**
     * Get the instance from the container.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public static function make($name, array $arguments)
    {
        return (new self($arguments))->offsetGet(
            self::applicationInstanceKey($name)
        );
    }

    /**
     * Get the application instance key.
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Exceptions\FaultException
     */
    protected static function applicationInstanceKey($name)
    {
        $applications = [
            'wework' => 'we_work.instance',
            'miniProgram' => 'mini_program.instance',
            'openPlatform' => 'open_platform.instance',
            'officialAccount' => 'official_account.instance',
        ];

        return $applications[$name] ?? (function () use ($name) {
            throw new FaultException("No application named '{$name}'");
        })();
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
