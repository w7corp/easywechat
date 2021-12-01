<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Kernel\Log;

use EasyWeChat\Kernel\ServiceContainer;
use InvalidArgumentException;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\FormattableHandlerInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\SlackWebhookHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;
use Monolog\Handler\WhatFailureGroupHandler;
use Monolog\Logger as Monolog;
use Psr\Log\LoggerInterface;

/**
 * Class LogManager.
 *
 * @author overtrue <i@overtrue.me>
 */
class LogManager implements LoggerInterface
{
    /**
     * @var \EasyWeChat\Kernel\ServiceContainer
     */
    protected $app;

    /**
     * The array of resolved channels.
     *
     * @var array
     */
    protected $channels = [];

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * The Log levels.
     *
     * @var array
     */
    protected $levels = [
        'debug' => Monolog::DEBUG,
        'info' => Monolog::INFO,
        'notice' => Monolog::NOTICE,
        'warning' => Monolog::WARNING,
        'error' => Monolog::ERROR,
        'critical' => Monolog::CRITICAL,
        'alert' => Monolog::ALERT,
        'emergency' => Monolog::EMERGENCY,
    ];

    /**
     * LogManager constructor.
     *
     * @param \EasyWeChat\Kernel\ServiceContainer $app
     */
    public function __construct(ServiceContainer $app)
    {
        $this->app = $app;
    }

    /**
     * Create a new, on-demand aggregate logger instance.
     *
     * @param array       $channels
     * @param string|null $channel
     *
     * @return \Psr\Log\LoggerInterface
     *
     * @throws \Exception
     */
    public function stack(array $channels, $channel = null)
    {
        return $this->createStackDriver(compact('channels', 'channel'));
    }

    /**
     * Get a log channel instance.
     *
     * @param string|null $channel
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function channel($channel = null)
    {
        return $this->driver($channel);
    }

    /**
     * Get a log driver instance.
     *
     * @param string|null $driver
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function driver($driver = null)
    {
        return $this->get($driver ?? $this->getDefaultDriver());
    }

    /**
     * Attempt to get the log from the local cache.
     *
     * @param string $name
     *
     * @return \Psr\Log\LoggerInterface
     *
     * @throws \Exception
     */
    protected function get($name)
    {
        try {
            return $this->channels[$name] ?? ($this->channels[$name] = $this->resolve($name));
        } catch (\Throwable $e) {
            $logger = $this->createEmergencyLogger();

            $logger->emergency('Unable to create configured logger. Using emergency logger.', [
                'exception' => $e,
            ]);

            return $logger;
        }
    }

    /**
     * Resolve the given log instance by name.
     *
     * @param string $name
     *
     * @return \Psr\Log\LoggerInterface
     *
     * @throws InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->app['config']->get(\sprintf('log.channels.%s', $name));

        if (is_null($config)) {
            throw new InvalidArgumentException(\sprintf('Log [%s] is not defined.', $name));
        }

        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($config);
        }

        $driverMethod = 'create'.ucfirst($config['driver']).'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        }

        throw new InvalidArgumentException(\sprintf('Driver [%s] is not supported.', $config['driver']));
    }

    /**
     * Create an emergency log handler to avoid white screens of death.
     *
     * @return \Monolog\Logger
     *
     * @throws \Exception
     */
    protected function createEmergencyLogger()
    {
        return new Monolog('EasyWeChat', $this->prepareHandlers([new StreamHandler(
            \sys_get_temp_dir().'/easywechat/easywechat.log',
            $this->level(['level' => 'debug'])
        )]));
    }

    /**
     * Call a custom driver creator.
     *
     * @param array $config
     *
     * @return mixed
     */
    protected function callCustomCreator(array $config)
    {
        return $this->customCreators[$config['driver']]($this->app, $config);
    }

    /**
     * Create an aggregate log driver instance.
     *
     * @param array $config
     *
     * @return \Monolog\Logger
     *
     * @throws \Exception
     */
    protected function createStackDriver(array $config)
    {
        $handlers = [];

        foreach ($config['channels'] ?? [] as $channel) {
            $handlers = \array_merge($handlers, $this->channel($channel)->getHandlers());
        }

        if ($config['ignore_exceptions'] ?? false) {
            $handlers = [new WhatFailureGroupHandler($handlers)];
        }

        return new Monolog($this->parseChannel($config), $handlers);
    }

    /**
     * Create an instance of the single file log driver.
     *
     * @param array $config
     *
     * @return \Psr\Log\LoggerInterface
     *
     * @throws \Exception
     */
    protected function createSingleDriver(array $config)
    {
        return new Monolog($this->parseChannel($config), [
            $this->prepareHandler(new StreamHandler(
                $config['path'],
                $this->level($config),
                $config['bubble'] ?? true,
                $config['permission'] ?? null,
                $config['locking'] ?? false
            ), $config),
        ]);
    }

    /**
     * Create an instance of the daily file log driver.
     *
     * @param array $config
     *
     * @return \Psr\Log\LoggerInterface
     */
    protected function createDailyDriver(array $config)
    {
        return new Monolog($this->parseChannel($config), [
            $this->prepareHandler(new RotatingFileHandler(
                $config['path'],
                $config['days'] ?? 7,
                $this->level($config),
                $config['bubble'] ?? true,
                $config['permission'] ?? null,
                $config['locking'] ?? false
            ), $config),
        ]);
    }

    /**
     * Create an instance of the Slack log driver.
     *
     * @param  array  $config
     *
     * @return \Psr\Log\LoggerInterface
     * @throws \Monolog\Handler\MissingExtensionException
     */
    protected function createSlackDriver(array $config)
    {
        return new Monolog($this->parseChannel($config), [
            $this->prepareHandler(new SlackWebhookHandler(
                $config['url'],
                $config['channel'] ?? null,
                $config['username'] ?? 'EasyWeChat',
                $config['attachment'] ?? true,
                $config['emoji'] ?? ':boom:',
                $config['short'] ?? false,
                $config['context'] ?? true,
                $this->level($config),
                $config['bubble'] ?? true,
                $config['exclude_fields'] ?? []
            ), $config),
        ]);
    }

    /**
     * Create an instance of the syslog log driver.
     *
     * @param array $config
     *
     * @return \Psr\Log\LoggerInterface
     */
    protected function createSyslogDriver(array $config)
    {
        return new Monolog($this->parseChannel($config), [
            $this->prepareHandler(new SyslogHandler(
                'EasyWeChat',
                $config['facility'] ?? LOG_USER,
                $this->level($config)
            ), $config),
        ]);
    }

    /**
     * Create an instance of the "error log" log driver.
     *
     * @param array $config
     *
     * @return \Psr\Log\LoggerInterface
     */
    protected function createErrorlogDriver(array $config)
    {
        return new Monolog($this->parseChannel($config), [
            $this->prepareHandler(
                new ErrorLogHandler(
                    $config['type'] ?? ErrorLogHandler::OPERATING_SYSTEM,
                    $this->level($config)
                )
            ),
        ]);
    }

    /**
     * Prepare the handlers for usage by Monolog.
     *
     * @param array $handlers
     *
     * @return array
     */
    protected function prepareHandlers(array $handlers): array
    {
        foreach ($handlers as $key => $handler) {
            $handlers[$key] = $this->prepareHandler($handler);
        }

        return $handlers;
    }

    /**
     * Prepare the handler for usage by Monolog.
     *
     * @param  \Monolog\Handler\HandlerInterface  $handler
     * @param  array  $config
     *
     * @return \Monolog\Handler\HandlerInterface
     */
    protected function prepareHandler(HandlerInterface $handler, array $config = [])
    {
        if (!isset($config['formatter'])) {
            if ($handler instanceof FormattableHandlerInterface) {
                $handler->setFormatter($this->formatter());
            }
        }

        return $handler;
    }

    /**
     * Get a Monolog formatter instance.
     *
     * @return \Monolog\Formatter\FormatterInterface
     */
    protected function formatter()
    {
        $formatter = new LineFormatter(null, null, true, true);
        $formatter->includeStacktraces();

        return $formatter;
    }

    /**
     * Extract the log channel from the given configuration.
     *
     * @param array $config
     *
     * @return string
     */
    protected function parseChannel(array $config): string
    {
        return $config['name'] ?? 'EasyWeChat';
    }

    /**
     * Parse the string level into a Monolog constant.
     *
     * @param array $config
     *
     * @return int
     *
     * @throws InvalidArgumentException
     */
    protected function level(array $config): int
    {
        $level = $config['level'] ?? 'debug';

        if (isset($this->levels[$level])) {
            return $this->levels[$level];
        }

        throw new InvalidArgumentException('Invalid log level.');
    }

    /**
     * Get the default log driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): ?string
    {
        return $this->app['config']['log.default'];
    }

    /**
     * Set the default log driver name.
     *
     * @param string $name
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['log.default'] = $name;
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param  string  $driver
     * @param \Closure $callback
     *
     * @return $this
     */
    public function extend(string $driver, \Closure $callback): LogManager
    {
        $this->customCreators[$driver] = $callback->bindTo($this, $this);

        return $this;
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     *
     * @throws \Exception
     */
    public function emergency($message, array $context = []): void
    {
        $this->driver()->emergency($message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     *
     * @throws \Exception
     */
    public function alert($message, array $context = []): void
    {
        $this->driver()->alert($message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array  $context
     *
     * @throws \Exception
     */
    public function critical($message, array $context = []): void
    {
        $this->driver()->critical($message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array  $context
     *
     * @throws \Exception
     */
    public function error($message, array $context = []): void
    {
        $this->driver()->error($message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array  $context
     *
     * @throws \Exception
     */
    public function warning($message, array $context = []): void
    {
        $this->driver()->warning($message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array  $context
     *
     * @throws \Exception
     */
    public function notice($message, array $context = []): void
    {
        $this->driver()->notice($message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array  $context
     *
     * @throws \Exception
     */
    public function info($message, array $context = []): void
    {
        $this->driver()->info($message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array  $context
     *
     * @throws \Exception
     */
    public function debug($message, array $context = []): void
    {
        $this->driver()->debug($message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @throws \Exception
     */
    public function log($level, $message, array $context = []): void
    {
        $this->driver()->log($level, $message, $context);
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @throws \Exception
     */
    public function __call($method, $parameters)
    {
        return $this->driver()->$method(...$parameters);
    }
}
