<?php


namespace EasyWeChat\Tests\Kernel\Traits;


use EasyWeChat\Kernel\Traits\InteractsWithCache;
use EasyWeChat\Tests\TestCase;
use Psr\SimpleCache\CacheInterface;


class InteractsWithCacheTest extends TestCase
{
    public function testBasicFeatures()
    {
        $cls = \Mockery::mock(InteractsWithCache::class);
        $this->assertInstanceOf(CacheInterface::class, $cls->getCache());

        $cache = \Mockery::mock(CacheInterface::class);
        $cls->setCache($cache);
        $this->assertSame($cache, $cls->getCache());
    }
}
