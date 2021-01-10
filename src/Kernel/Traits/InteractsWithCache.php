<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\ServiceContainer;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface as SimpleCacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\Cache\Simple\FilesystemCache;

//TODO: PSR16 Cache
trait InteractsWithCache
{
    protected SimpleCacheInterface $cache;

    public function getCache(): SimpleCacheInterface
    {
        if ($this->cache) {
            return $this->cache;
        }

        if (property_exists($this, 'app') && $this->app instanceof ServiceContainer && isset($this->app['cache'])) {
            $this->setCache($this->app['cache']);

            // Fix PHPStan error
            assert($this->cache instanceof \Psr\SimpleCache\CacheInterface);

            return $this->cache;
        }

        return $this->cache = $this->createDefaultCache();
    }

    public function setCache(SimpleCacheInterface|CacheItemPoolInterface $cache): static
    {
        if (empty(\array_intersect([SimpleCacheInterface::class, CacheItemPoolInterface::class], \class_implements($cache)))) {
            throw new InvalidArgumentException(\sprintf('The cache instance must implements %s or %s interface.', SimpleCacheInterface::class, CacheItemPoolInterface::class));
        }

        if ($cache instanceof CacheItemPoolInterface) {
            if (!$this->isSymfony43OrHigher()) {
                throw new InvalidArgumentException(sprintf('The cache instance must implements %s', SimpleCacheInterface::class));
            }
            $cache = new Psr16Cache($cache);
        }

        $this->cache = $cache;

        return $this;
    }

    protected function createDefaultCache(): Psr16Cache|FilesystemCache
    {
        if ($this->isSymfony43OrHigher()) {
            return new Psr16Cache(new FilesystemAdapter('easywechat', 1500));
        }

        return new FilesystemCache();
    }

    protected function isSymfony43OrHigher(): bool
    {
        return \class_exists('Symfony\Component\Cache\Psr16Cache');
    }
}
