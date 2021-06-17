<?php

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Config;
use EasyWeChat\Kernel\Contracts\Config as ConfigInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

trait InteractWithCache
{
    protected ?CacheInterface $cache = null;

    public function setCache(CacheInterface $cache): static
    {
        $this->cache = $cache;

        return $this;
    }

    public function getCache(): CacheInterface
    {
        if (!$this->cache) {
            $this->cache = new Psr16Cache(
                new FilesystemAdapter(
                    $this->config->get('cache.namespace', 'easywechat'),
                    $this->config->get('cache.lifetime', 1500),
                )
            );
        }

        return $this->cache;
    }
}