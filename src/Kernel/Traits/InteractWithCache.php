<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

trait InteractWithCache
{
    protected ?CacheInterface $cache = null;

    protected int $cacheLifetime = 1500;

    protected string $cacheNamespace = 'easywechat';

    public function getCacheLifetime(): int
    {
        return $this->cacheLifetime;
    }

    public function setCacheLifetime(int $cacheLifetime): void
    {
        $this->cacheLifetime = $cacheLifetime;
    }

    public function getCacheNamespace(): string
    {
        return $this->cacheNamespace;
    }

    public function setCacheNamespace(string $cacheNamespace): void
    {
        $this->cacheNamespace = $cacheNamespace;
    }

    public function setCache(CacheInterface $cache): static
    {
        $this->cache = $cache;

        return $this;
    }

    public function getCache(): CacheInterface
    {
        if (! $this->cache) {
            $this->cache = new Psr16Cache(new FilesystemAdapter($this->cacheNamespace, $this->cacheLifetime));
        }

        return $this->cache;
    }
}
