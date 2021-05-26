<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\ServiceContainer;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface as SimpleCacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

//TODO: PSR16 Cache
trait InteractsWithCache
{
    /**
     * @var \Psr\SimpleCache\CacheInterface|null
     */
    protected ?SimpleCacheInterface $cache = null;

    /**
     * @return \Psr\SimpleCache\CacheInterface|null
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function getCache(): SimpleCacheInterface | null
    {
        if ($this->cache) {
            return $this->cache;
        }

        if (
            property_exists($this, 'app')
            &&
            $this->app instanceof ServiceContainer
            &&
            $cache = $this->app['cache'] ?? null
        ) {
            $this->setCache($cache);

            return $this->cache;
        }

        return $this->cache = $this->createDefaultCache();
    }

    public function setCache(SimpleCacheInterface | CacheItemPoolInterface $cache): static
    {
        if (
            !$cache instanceof SimpleCacheInterface
            &&
            !$cache instanceof CacheItemPoolInterface
        ) {
            throw new InvalidArgumentException(
                \sprintf(
                    'The cache instance must implements %s or %s interface.',
                    SimpleCacheInterface::class,
                    CacheItemPoolInterface::class
                )
            );
        }

        if ($cache instanceof CacheItemPoolInterface) {
            $cache = new Psr16Cache($cache);
        }

        $this->cache = $cache;

        return $this;
    }

    protected function createDefaultCache(): Psr16Cache | FilesystemCache
    {
        if (
            property_exists($this, 'app')
            &&
            $this->app instanceof ServiceContainer
            &&
            $cacheConfig = $this->app->getConfig()['cache'] ?? []
        ) {
            $namespace = $cacheConfig['namespace'];
            $lifeTime = $cacheConfig['life_time'];
        }

        return new Psr16Cache(
            new FilesystemAdapter(
                $namespace,
                $lifeTime
            )
        );
    }
}
