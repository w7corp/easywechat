<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
use EasyWeChat\Kernel\Decorators\FinallyResult;
use EasyWeChat\Kernel\Decorators\TerminateResult;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\ServiceContainer;
use function EasyWeChat\Kernel\throw_if;

trait Observable
{
    /**
     * @var array
     */
    protected array $handlers = [];

    /**
     * @return array
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }

    /**
     * @param callable|\EasyWeChat\Kernel\Contracts\EventHandlerInterface|string $handler
     *
     * @return $this
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function withHandler(
        callable | EventHandlerInterface | string $handler
    ): static {
        $handler = $this->makeClosure($handler);

        $this->handlers[$this->getHandlerHash($handler)] = $handler;

        return $this;
    }

    /**
     * @param array  $handlers
     *
     * @return $this
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function withHandlers(array $handlers): static
    {
        foreach (
            $handlers as $handler
        ) {
            $this->withHandler($handler);
        }

        return $this;
    }

    /**
     * @param callable|\EasyWeChat\Kernel\Contracts\EventHandlerInterface|string $handler
     *
     * @return $this
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function withoutHandler(
        callable | EventHandlerInterface | string $handler
    ): static {
        $handler = $this->makeClosure($handler);

        unset($this->handlers[$this->getHandlerHash($handler)]);

        return $this;
    }

    /**
     * @param array|null $handlers
     *
     * @return $this
     *
     * @throws \Throwable
     */
    public function withoutHandlers(array $handlers = null): static
    {
        if (!is_array($handlers)) {
            $handlers = $this->handlers;
        }

        foreach ($handlers as $handler) {
            $this->withoutHandler($handler);
        }

        return $this;
    }

    /**
     * @param                                                                    $value
     * @param callable|\EasyWeChat\Kernel\Contracts\EventHandlerInterface|string $handler
     *
     * @return $this
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function when(
        $value,
        callable | EventHandlerInterface | string $handler
    ): static {
        if ($value instanceof \Closure) {
            $value = $value->bindTo($this);
        }

        if ($value) {
            return $this->withHandler($handler);
        }

        return $this;
    }

    /**
     * @param                                                                    $value
     * @param callable|\EasyWeChat\Kernel\Contracts\EventHandlerInterface|string $handler
     *
     * @return $this
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function unless(
        $value,
        callable | EventHandlerInterface | string $handler
    ): static {
        if ($value instanceof \Closure) {
            $value = $value->bindTo($this);
        }

        return $this->when(!$value, $handler);
    }

    /**
     * @param array $payload
     *
     * @return mixed
     */
    public function handle(array $payload): mixed
    {
        foreach ($this->handlers as $handler) {
            try {
                $response = \call_user_func_array($handler, [$payload]);

                switch (true) {
                    case $response instanceof TerminateResult:
                        return $response->content;
                    case true === $response:
                        break;
                    case false === $response:
                        break 2;
                    case $response && !(($result ?? null) instanceof FinallyResult):
                        $result = $response;
                }
            } catch (\Exception $e) {
                if (
                    property_exists($this, 'app')
                    &&
                    $this->app instanceof ServiceContainer
                ) {
                    $this->app['logger']->error(
                        $e->getCode().': '.$e->getMessage(),
                        [
                            'code' => $e->getCode(),
                            'message' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                        ]
                    );
                }
            }
        }

        return ($result ?? null) instanceof FinallyResult ? $result->content : $result;
    }

    /**
     * @param callable|\EasyWeChat\Kernel\Contracts\EventHandlerInterface|string $handler
     *
     * @return string
     *
     * @throws \Throwable
     */
    protected function getHandlerHash(
        callable | EventHandlerInterface | string $handler
    ): string {
        if (is_string($handler)) {
            return $handler;
        }

        if (!\is_array($handler)) {
            return spl_object_hash($handler);
        }

        throw_if(2 !== \count($handler), InvalidArgumentException::class);

        return is_string($handler[0])
                ? $handler[0].'::'.$handler[1] : get_class($handler[0]).$handler[1];
    }

    /**
     * @param callable|\EasyWeChat\Kernel\Contracts\EventHandlerInterface|string $handler
     *
     * @return \Closure
     *
     * @throws \ReflectionException
     * @throws \Throwable
     */
    protected function makeClosure(
        callable | EventHandlerInterface | string $handler
    ): \Closure {
        if (is_callable($handler)) {
            return $handler;
        }

        if ($handler instanceof EventHandlerInterface) {
            return function () use ($handler) {
                return $handler->handle(...func_get_args());
            };
        }

        throw_if(
            !\is_string($handler),
            InvalidArgumentException::class,
            'No valid handler is found in arguments.'
        );

        throw_if(
            !class_exists($handler),
            InvalidArgumentException::class,
            sprintf('Class "%s" not exists.', $handler)
        );

        throw_if(
            !in_array(EventHandlerInterface::class, (new \ReflectionClass($handler))->getInterfaceNames(), true),
            InvalidArgumentException::class,
            sprintf('Class "%s" not an instance of "%s".', $handler, EventHandlerInterface::class)
        );

        return function ($payload) use ($handler) {
            return (new $handler($this))->handle($payload);
        };
    }
}
