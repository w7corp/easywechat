<?php

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Config;
use EasyWeChat\Kernel\Contracts\Config as ConfigInterface;

trait InteractWithConfig
{
    protected ?ConfigInterface $config = null;

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function __construct(array | ConfigInterface $config)
    {
        if (\is_array($config)) {
            $config = new Config($config);
        }

        $this->config = $config;
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