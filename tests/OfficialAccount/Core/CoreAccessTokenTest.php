<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Core;

use Doctrine\Common\Cache\Cache;
use EasyWeChat\Applications\Base\Core\Http;
use EasyWeChat\Applications\OfficialAccount\Core\AccessToken;
use EasyWeChat\Tests\TestCase;

class CoreAccessTokenTest extends TestCase
{
    public function testGetToken()
    {
        $cache = \Mockery::mock(Cache::class, function ($mock) {
            $mock->shouldReceive('fetch')->andReturn('thisIsACachedToken');
            $mock->shouldReceive('save')->andReturnUsing(function ($key, $token, $expire) {
                return $token;
            });
        });

        $http = \Mockery::mock(Http::class.'[get]', function ($mock) {
            $mock->shouldReceive('get')->andReturn(json_encode([
                    'access_token' => 'thisIsATokenFromHttp',
                    'expires_in' => 7200,
                ]));
        });

        // cached
        $accessToken = new AccessToken('appId', 'secret');
        $accessToken->setCache($cache);
        $accessToken->setHttp($http);

        $this->assertSame('thisIsACachedToken', $accessToken->getToken());
        // forceRefresh
        $this->assertSame('thisIsATokenFromHttp', $accessToken->getToken(true));
    }

    /**
     * Test getToken() without cache.
     */
    public function testNonCachedGetToken()
    {
        $cacheObj = new \stdClass();

        // non-cached
        $cache = \Mockery::mock(Cache::class, function ($mock) use ($cacheObj) {
            $mock->shouldReceive('fetch')->andReturnUsing(function ($cacheKey) {
            });

            $mock->shouldReceive('save')->andReturnUsing(function ($key, $token, $expire) use ($cacheObj) {
                $cacheObj->cacheKey = $key;
                $cacheObj->token = $token;
                $cacheObj->expire = $expire;

                return $token;
            });
        });

        $http = \Mockery::mock(Http::class.'[get]', function ($mock) {
            $mock->shouldReceive('get')->andReturn(json_encode([
                    'access_token' => 'thisIsATokenFromHttp',
                    'expires_in' => 7200,
                ]));
        });

        $accessToken = new AccessToken('appId', 'secret');
        $accessToken->setCache($cache);
        $accessToken->setHttp($http);

        $this->assertSame('thisIsATokenFromHttp', $accessToken->getToken());
        $this->assertSame('thisIsATokenFromHttp', $cacheObj->token);
        $this->assertSame(5700, $cacheObj->expire);

        $http = \Mockery::mock(Http::class.'[get]', function ($mock) {
            $mock->shouldReceive('get')->andReturn(json_encode([
                    'foo' => 'bar', // without "access_token"
                ]));
        });

        $accessToken = new AccessToken('appId', 'secret');
        $accessToken->setCache($cache);
        $accessToken->setHttp($http);

        $this->expectException(\EasyWeChat\Exceptions\HttpException::class);
        $this->expectExceptionMessage('Request AuthorizerAccessToken fail. response: {"foo":"bar"}');

        $accessToken->getToken();
        $this->fail();
    }

    public function testGetterAndSetter()
    {
        $accessToken = new AccessToken('appId', 'secret');

        $this->assertSame('secret', $accessToken->getClientSecret());
        $this->assertSame('appId', $accessToken->getClientId());

        $this->assertInstanceOf(\Doctrine\Common\Cache\FilesystemCache::class, $accessToken->getCache());

        $cache = \Mockery::mock(Cache::class, function ($mock) {
            $mock->shouldReceive('fetch')->andReturn('thisIsACachedToken');
            $mock->shouldReceive('save')->andReturnUsing(function ($key, $token, $expire) {
                return $token;
            });
        });

        $accessToken->setCache($cache);
        $this->assertSame($cache, $accessToken->getCache());

        $this->assertSame('access_token', $accessToken->getQueryName());
        $this->assertArrayHasKey('access_token', $accessToken->getQueryFields());

        $accessToken->setQueryName('foo');

        $this->assertSame('foo', $accessToken->getQueryName());
        $this->assertArrayHasKey('foo', $accessToken->getQueryFields());
        $this->assertArrayNotHasKey('access_token', $accessToken->getQueryFields());
    }

    public function testSetToken()
    {
        $accessToken = new AccessToken('appId', 'secret');

        $this->assertSame('secret', $accessToken->getClientSecret());
        $this->assertSame('appId', $accessToken->getClientId());

        $this->assertInstanceOf(\Doctrine\Common\Cache\FilesystemCache::class, $accessToken->getCache());

        $cache = \Mockery::mock(Cache::class, function ($mock) {
            $mock->shouldReceive('fetch')->andReturn('foo');
            $mock->shouldReceive('save')->with('easywechat.common.access_token.appId', 'foo', 5700)->andReturnUsing(function ($key, $token, $expire) {
                return $token;
            });
        });

        $accessToken->setCache($cache);

        $accessToken->setToken('foo');

        $this->assertSame('foo', $accessToken->getToken());
    }
}
