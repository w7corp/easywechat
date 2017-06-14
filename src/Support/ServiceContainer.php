<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Support;

use EasyWeChat\Config\Repository as Config;
use EasyWeChat\Support\Log;
use Monolog\Handler\{HandlerInterface, NullHandler, StreamHandler};
use Monolog\Logger;
use Pimple\Container;

/**
 * Class ServiceContainer.
 *
 * @author overtrue <i@overtrue.me>
 */
class ServiceContainer extends Container
{
    /**
     * @var array
     */
    protected $providers = [];

    /**
     * Constructor.
     *
     * @param array|\EasyWeChat\Config\Repository $config
     */
    public function __construct($config)
    {
        parent::__construct();

        $this['config'] = ($config instanceof Config) ? $config : new Config($config);

        $this->registerProviders()->registerLogger();
    }

    /**
     * Add a provider.
     *
     * @param string $provider
     *
     * @return Factory
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
