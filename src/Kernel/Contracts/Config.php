<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Contracts;

interface Config extends \ArrayAccess
{
    public function all(): array;
    public function has(string $key): bool;
    public function set(array | string $key, mixed $value = null): void;
    public function get(array | string $key, mixed $default = null): mixed;
    public function prepend(string $key, mixed $value): void;
    public function push(string $key, mixed $value): void;
}
