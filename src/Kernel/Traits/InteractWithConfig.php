<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Config;
use EasyWeChat\Kernel\Contracts\Config as ConfigInterface;

use function intval;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_scalar;
use function is_string;

trait InteractWithConfig
{
    protected ConfigInterface $config;

    /**
     * @param  array<string,mixed>|ConfigInterface  $config
     */
    public function __construct(array|ConfigInterface $config)
    {
        $this->config = is_array($config) ? new Config($config) : $config;
    }

    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    public function setConfig(ConfigInterface $config): static
    {
        $this->config = $config;
        $this->afterConfigUpdated();

        return $this;
    }

    protected function afterConfigUpdated(): void
    {
    }

    protected function getStringConfig(string $key, string $default = ''): string
    {
        $value = $this->config->get($key, $default);

        if (is_scalar($value) || $value === null) {
            return (string) $value;
        }

        return $default;
    }

    protected function getBoolConfig(string $key, bool $default = false): bool
    {
        $value = $this->config->get($key, $default);

        if (is_bool($value)) {
            return $value;
        }

        return $default;
    }

    protected function getIntConfig(string $key, int $default = 0): int
    {
        $value = $this->config->get($key, $default);

        if (is_int($value)) {
            return $value;
        }

        if (is_bool($value) || is_float($value) || is_string($value)) {
            return intval($value);
        }

        return $default;
    }

    /**
     * @param  array<string>  $default
     * @return array<string>
     */
    protected function getStringListConfig(string $key, array $default = []): array
    {
        $values = [];

        foreach ((array) $this->config->get($key, $default) as $value) {
            if (is_scalar($value) || $value === null) {
                $values[] = (string) $value;
            }
        }

        return $values;
    }
}
