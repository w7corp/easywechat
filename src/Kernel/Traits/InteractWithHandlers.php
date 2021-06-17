<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;

trait InteractWithHandlers
{
    protected array $handlers = [];

    public function getHandlers(): array
    {
        return $this->handlers;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function with(callable | string $handler): static
    {
        return $this->withHandler(...\func_get_args());
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function without(callable | string $handler): static
    {
        return $this->withoutHandler(...\func_get_args());
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function when($value, callable | string $handler): static
    {
        if (\is_callable($value)) {
            $value = \call_user_func($value, $this);
        }

        !!$value && $this->withHandler($handler);

        return $this;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function unless($value, callable | string $handler): static
    {
        if (\is_callable($value)) {
            $value = \call_user_func($value, $this);
        }

        !$value && $this->withHandler($handler);

        return $this;
    }

    public function handle($result, ...$payload): mixed
    {
        $next = \is_callable($result) ? $result : fn ($result) => $result;

        foreach ($this->handlers as $handler) {
            $next = fn (...$payload) => $handler(...[...$payload, $next]);
        }

        return $next($result);
    }

    public function has(callable | string $handler): bool
    {
        return \array_key_exists($this->getHandlerHash($handler), $this->handlers);
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function withHandler(callable | string $handler): static
    {
        $handler = $this->makeClosure($handler);

        $this->handlers[$this->getHandlerHash($handler)] = $handler;

        return $this;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function withHandlers(array $handlers): static
    {
        foreach ($handlers as $handler) {
            $this->withHandler($handler);
        }

        return $this;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function withoutHandler(callable | string $handler): static
    {
        $handler = $this->makeClosure($handler);

        unset($this->handlers[$this->getHandlerHash($handler)]);

        return $this;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
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

    protected function getHandlerHash(callable | string $handler): string
    {
        return match (true) {
            \is_string($handler) => $handler,
            \is_array($handler) => is_string($handler[0]) ? $handler[0] . '::' . $handler[1] : get_class($handler[0]) . $handler[1],
            $handler instanceof \Closure => \spl_object_hash($handler),
        };
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    protected function makeClosure(callable | string $handler): callable | string
    {
        if (is_callable($handler)) {
            return $handler;
        }

        if (class_exists($handler) && \method_exists($handler, '__invoke')) {
            return $handler;
        }

        throw new InvalidArgumentException(sprintf('Invalid handler: %s.', $handler));
    }
}
