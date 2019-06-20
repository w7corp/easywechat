<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Kernel\Traits;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Traits\InteractsWithCache;
use EasyWeChat\Tests\TestCase;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\Cache\Psr16Cache;

class InteractsWithCacheTest extends TestCase
{
    public function testBasicFeatures()
    {
        $trait = \Mockery::mock(InteractsWithCache::class);
        $this->assertInstanceOf(CacheInterface::class, $trait->getCache());

        $cache = \Mockery::mock(CacheInterface::class);
        $trait->setCache($cache);
        $this->assertSame($cache, $trait->getCache());

        if (\class_exists('Symfony\Component\Cache\Psr16Cache')) {
            $this->doTestPsr6Bridge();
        }
    }

    /**
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function doTestPsr6Bridge()
    {
        $trait = \Mockery::mock(InteractsWithCache::class);

        $this->assertInstanceOf(Psr16Cache::class, $trait->getCache());

        $psr6Cache = new NullAdapter();

        $trait->setCache($psr6Cache);

        $this->assertInstanceOf(CacheInterface::class, $trait->getCache());

        // invalid instance
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The cache instance must implements Psr\SimpleCache\CacheInterface or Psr\Cache\CacheItemPoolInterface interface.');

        $trait->setCache(new \stdClass());
    }
}
