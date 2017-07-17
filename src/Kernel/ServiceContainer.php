<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Kernel;

use EasyWeChat\Kernel\Support\Log;
use GuzzleHttp\Client;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ServiceContainer.
 *
 * @author overtrue <i@overtrue.me>
 *
 * @property \EasyWeChat\Kernel\Config $config
 */
class ServiceContainer extends Container
{
    /**
     * @var array
     */
    protected $providers = [];

    /**
     * @var array
     */
    protected $defaultConfig = [];

    /**
     * @var array
     */
    protected $globalConfig = [
        // http://docs.guzzlephp.org/en/stable/request-options.html
        'http' => [
            'timeout' => 5.0,
            'base_uri' => 'https://api.weixin.qq.com/',
        ],
    ];

    /**
     * Constructor.
     *
     * @param array $config
     * @param array $prepends
     */
    public function __construct(array $config, array $prepends = [])
    {
        parent::__construct($prepends);

        $this->registerConfig($config);

        $this->beforeRegistered();

        $this->registerProviders()
            ->registerLogger()
            ->registerRequest()
            ->registerHttpClient();

        $this->afterRegistered();
    }

    /**
     * Add a provider.
     *
     * @param string $provider
     *
     * @return $this
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
     * Register config.
     *
     * @param array $config
     *
     * @return $this
     */
    protected function registerConfig(array $config)
    {
        $this['config'] = function () use ($config) {
            return new Config(
                array_replace_recursive($this->globalConfig, $this->defaultConfig, $config)
            );
        };

        return $this;
    }

    /**
     * Register service providers.
     *
     * @return $this
     */
    protected function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }

        return $this;
    }

    /**
     * Register request.
     *
     * @return $this
     */
    protected function registerRequest()
    {
        $this['request'] = function () {
            return Request::createFromGlobals();
        };

        return $this;
    }

    /**
     * Register logger.
     *
     * @return $this
     */
    protected function registerLogger()
    {
        if (Log::hasLogger()) {
            return $this;
        }

        $logger = new Logger('easywechat');

        if (!$this['config']['debug'] || defined('PHPUNIT_RUNNING') || php_sapi_name() === 'cli') {
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

        return $this;
    }

    /**
     * @return $this
     */
    protected function registerHttpClient()
    {
        $this['http_client'] = function ($app) {
            return new Client($app['config']->get('http', []));
        };

        return $this;
    }

    protected function afterRegistered()
    {
    }

    protected function beforeRegistered()
    {
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
}
