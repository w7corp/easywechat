<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Log;

use EasyWeChat\Kernel\ServiceContainer;
use InvalidArgumentException;
use Monolog\Formatter\FormatterInterface;
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

class LogManager implements LoggerInterface
{
    protected array $channels = [];
    protected array $customCreators = [];
    protected array $levels = [
        'debug' => Monolog::DEBUG,
        'info' => Monolog::INFO,
        'notice' => Monolog::NOTICE,
        'warning' => Monolog::WARNING,
        'error' => Monolog::ERROR,
        'critical' => Monolog::CRITICAL,
        'alert' => Monolog::ALERT,
        'emergency' => Monolog::EMERGENCY,
    ];

    public function __construct(
        public ServiceContainer $app
    ) {
    }

    public function stack(array $channels, $channel = null): LoggerInterface
    {
        return $this->createStackDriver(compact('channels', 'channel'));
    }

    public function channel($channel = null): mixed
    {
        return $this->driver($channel);
    }

    public function driver(?string $driver = null): LoggerInterface
    {
        return $this->get($driver ?? $this->getDefaultDriver());
    }

    protected function get(string $name): LoggerInterface
    {
        try {
            return $this->channels[$name] ?? ($this->channels[$name] = $this->resolve($name));
        } catch (\Throwable $e) {
            $logger = $this->createEmergencyLogger();

            $logger->emergency(
                'Unable to create configured logger. Using emergency logger.',
                [
                    'exception' => $e,
                ]
            );

            return $logger;
        }
    }

    protected function resolve(string $name): LoggerInterface
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

    protected function createEmergencyLogger(): LoggerInterface
    {
        return new Monolog(
            'EasyWeChat', $this->prepareHandlers(
            [
                new StreamHandler(
                    \sys_get_temp_dir().'/easywechat/easywechat.log',
                    $this->level(['level' => 'debug'])
                ),
            ]
        )
        );
    }

    protected function callCustomCreator(array $config): LoggerInterface
    {
        return $this->customCreators[$config['driver']]($this->app, $config);
    }

    protected function createStackDriver(array $config): LoggerInterface
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

    protected function createSingleDriver(array $config): LoggerInterface
    {
        return new Monolog(
            $this->parseChannel($config), [
            $this->prepareHandler(
                new StreamHandler(
                    $config['path'],
                    $this->level($config),
                    $config['bubble'] ?? true,
                    $config['permission'] ?? null,
                    $config['locking'] ?? false
                ),
                $config
            ),
        ]
        );
    }

    protected function createDailyDriver(array $config): LoggerInterface
    {
        return new Monolog(
            $this->parseChannel($config), [
            $this->prepareHandler(
                new RotatingFileHandler(
                    $config['path'],
                    $config['days'] ?? 7,
                    $this->level($config),
                    $config['bubble'] ?? true,
                    $config['permission'] ?? null,
                    $config['locking'] ?? false
                ),
                $config
            ),
        ]
        );
    }

    protected function createSlackDriver(array $config): LoggerInterface
    {
        return new Monolog(
            $this->parseChannel($config), [
            $this->prepareHandler(
                new SlackWebhookHandler(
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
                ),
                $config
            ),
        ]
        );
    }

    protected function createSyslogDriver(array $config): LoggerInterface
    {
        return new Monolog(
            $this->parseChannel($config), [
            $this->prepareHandler(
                new SyslogHandler(
                    'EasyWeChat',
                    $config['facility'] ?? LOG_USER,
                    $this->level($config)
                ),
                $config
            ),
        ]
        );
    }

    protected function createErrorlogDriver(array $config): LoggerInterface
    {
        return new Monolog(
            $this->parseChannel($config), [
            $this->prepareHandler(
                new ErrorLogHandler(
                    $config['type'] ?? ErrorLogHandler::OPERATING_SYSTEM,
                    $this->level($config)
                )
            ),
        ]
        );
    }

    protected function prepareHandlers(array $handlers): array
    {
        foreach ($handlers as $key => $handler) {
            $handlers[$key] = $this->prepareHandler($handler);
        }

        return $handlers;
    }

    protected function prepareHandler(HandlerInterface $handler, array $config = []): HandlerInterface
    {
        if (!isset($config['formatter'])) {
            if ($handler instanceof FormattableHandlerInterface) {
                $handler->setFormatter($this->formatter());
            }
        }

        return $handler;
    }

    protected function formatter(): FormatterInterface
    {
        $formatter = new LineFormatter(null, null, true, true);
        $formatter->includeStacktraces();

        return $formatter;
    }

    protected function parseChannel(array $config): string
    {
        return $config['name'] ?? 'EasyWeChat';
    }

    protected function level(array $config): string
    {
        $level = $config['level'] ?? 'debug';

        if (isset($this->levels[$level])) {
            return $this->levels[$level];
        }

        throw new InvalidArgumentException('Invalid log level.');
    }

    public function getDefaultDriver(): string
    {
        return $this->app['config']['log.default'] ?? '';
    }

    /**
     * Set the default log driver name.
     *
     * @param  string  $name
     */
    public function setDefaultDriver(string $name)
    {
        $this->app['config']['log.default'] = $name;
    }

    public function extend(string $driver, \Closure $callback): static
    {
        $this->customCreators[$driver] = $callback->bindTo($this, $this);

        return $this;
    }

    public function emergency($message, array $context = []): void
    {
        $this->driver()->emergency($message, $context);
    }

    public function alert($message, array $context = []): void
    {
        $this->driver()->alert($message, $context);
    }

    public function critical($message, array $context = []): void
    {
        $this->driver()->critical($message, $context);
    }

    public function error($message, array $context = []):void
    {
        $this->driver()->error($message, $context);
    }

    public function warning($message, array $context = []): void
    {
        $this->driver()->warning($message, $context);
    }

    public function notice($message, array $context = []): void
    {
        $this->driver()->notice($message, $context);
    }

    public function info($message, array $context = []): void
    {
        $this->driver()->info($message, $context);
    }

    public function debug($message, array $context = []): void
    {
        $this->driver()->debug($message, $context);
    }

    public function log($level, $message, array $context = []): void
    {
        $this->driver()->log($level, $message, $context);
    }

    public function __call($method, $parameters): mixed
    {
        return $this->driver()->$method(...$parameters);
    }
}
