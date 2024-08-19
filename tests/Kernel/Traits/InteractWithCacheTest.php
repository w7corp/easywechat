<?php

namespace EasyWeChat\Tests\Kernel\Traits;

use EasyWeChat\Kernel\Traits\InteractWithCache;
use EasyWeChat\Tests\TestCase;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Psr16Cache;

class InteractWithCacheTest extends TestCase
{
    public function test_get_and_set_cache()
    {
        $app = new DummyClassForInteractWithCacheTest;

        $this->assertInstanceOf(CacheInterface::class, $app->getCache());
        $this->assertSame($app->getCache(), $app->getCache());

        // set
        $cache = \Mockery::mock(Psr16Cache::class);
        $app->setCache($cache);
        $this->assertSame($cache, $app->getCache());
    }
}

class DummyClassForInteractWithCacheTest
{
    use InteractWithCache;
}
