<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Decorators\FinallyResult;
use EasyWeChat\Kernel\Decorators\TerminateResult;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\OfficialAccount\Contracts\Handler;
use EasyWeChat\OfficialAccount\Contracts\Message;
use function EasyWeChat\Kernel\throw_if;

trait Observable
{
    protected array $handlers = [];

    public function getHandlers(): array
    {
        return $this->handlers;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function withHandler(
        callable|Handler|string $handler
    ): static {
        $handler = $this->makeClosure($handler);

        $this->handlers[$this->getHandlerHash($handler)] = $handler;

        return $this;
    }

    /**
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
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function withoutHandler(
        callable|Handler|string $handler
    ): static {
        $handler = $this->makeClosure($handler);

        unset($this->handlers[$this->getHandlerHash($handler)]);

        return $this;
    }

    /**
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
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function when(
        $value,
        callable|Handler|string $handler
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
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function unless(
        $value,
        callable|Handler|string $handler
    ): static {
        if ($value instanceof \Closure) {
            $value = $value->bindTo($this);
        }

        return $this->when(!$value, $handler);
    }

    public function handle(Message $payload): mixed
    {
        foreach ($this->handlers as $handler) {
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
        }

        return ($result ?? null) instanceof FinallyResult ? $result->content : $result;
    }

    /**
     * @throws \Throwable
     */
    protected function getHandlerHash(
        callable|Handler|string $handler
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
     * @throws \ReflectionException
     * @throws \Throwable
     */
    protected function makeClosure(
        callable|Handler|string $handler
    ): \Closure {
        if (is_callable($handler)) {
            return $handler;
        }

        if ($handler instanceof Handler) {
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
            !in_array(Handler::class, (new \ReflectionClass($handler))->getInterfaceNames(), true),
            InvalidArgumentException::class,
            sprintf('Class "%s" not an instance of "%s".', $handler, Handler::class)
        );

        return function ($message) use ($handler) {
            return (new $handler($this->application))->handle($message);
        };
    }
}
