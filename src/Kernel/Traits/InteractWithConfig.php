<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Config;
use EasyWeChat\Kernel\Contracts\Config as ConfigInterface;

trait InteractWithConfig
{
    protected ConfigInterface $config;

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function __construct(array | ConfigInterface $config)
    {
        $this->config = \is_array($config) ? new Config($config) : $config;
    }

    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    public function setConfig(ConfigInterface $config): static
    {
        $this->config = $config;

        return $this;
    }
}
