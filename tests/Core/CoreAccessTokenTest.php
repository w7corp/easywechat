<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Doctrine\Common\Cache\Cache;
use EasyWeChat\Core\AccessToken;
use EasyWeChat\Core\Http;

class CoreAccessTokenTest extends TestCase
{
    public function testGetToken()
    {
        $cache = Mockery::mock(Cache::class, function ($mock) {
            $mock->shouldReceive('fetch')->andReturn('thisIsACachedToken');
            $mock->shouldReceive('save')->andReturnUsing(function ($key, $token, $expire) {
                return $token;
            });
        });

        $http = Mockery::mock(Http::class.'[get]', function ($mock) {
            $mock->shouldReceive('get')->andReturn(json_encode([
                    'access_token' => 'thisIsATokenFromHttp',
                    'expires_in' => 7200,
                ]));
        });

        // cached
        $accessToken = new AccessToken('appId', 'secret', $cache);
        $accessToken->setHttp($http);

        $this->assertEquals('thisIsACachedToken', $accessToken->getToken());
        // forceRefresh
        $this->assertEquals('thisIsATokenFromHttp', $accessToken->getToken(true));
    }

    /**
     * Test getToken() without cache.
     */
    public function testNonCachedGetToken()
    {
        $cacheObj = new stdClass();

        // non-cached
        $cache = Mockery::mock(Cache::class, function ($mock) use ($cacheObj) {
            $mock->shouldReceive('fetch')->andReturnUsing(function ($cacheKey) {
                return;
            });

            $mock->shouldReceive('save')->andReturnUsing(function ($key, $token, $expire) use ($cacheObj) {
                $cacheObj->cacheKey = $key;
                $cacheObj->token = $token;
                $cacheObj->expire = $expire;

                return $token;
            });
        });

        $http = Mockery::mock(Http::class.'[get]', function ($mock) {
            $mock->shouldReceive('get')->andReturn(json_encode([
                    'access_token' => 'thisIsATokenFromHttp',
                    'expires_in' => 7200,
                ]));
        });

        $accessToken = new AccessToken('appId', 'secret', $cache);
        $accessToken->setHttp($http);

        $this->assertEquals('thisIsATokenFromHttp', $accessToken->getToken());
        $this->assertEquals('thisIsATokenFromHttp', $cacheObj->token);
        $this->assertEquals(5700, $cacheObj->expire);

        $http = Mockery::mock(Http::class.'[get]', function ($mock) {
            $mock->shouldReceive('get')->andReturn(json_encode([
                    'foo' => 'bar', // without "access_token"
                ]));
        });

        $accessToken = new AccessToken('appId', 'secret', $cache);
        $accessToken->setHttp($http);

        $this->setExpectedException(\EasyWeChat\Core\Exceptions\HttpException::class, 'Request AccessToken fail. response: {"foo":"bar"}');
        $accessToken->getToken();
        $this->fail();
    }

    public function testGetterAndSetter()
    {
        $accessToken = new AccessToken('appId', 'secret');

        $this->assertEquals('secret', $accessToken->getSecret());
        $this->assertEquals('appId', $accessToken->getAppId());

        $this->assertInstanceOf(\Doctrine\Common\Cache\FilesystemCache::class, $accessToken->getCache());

        $cache = Mockery::mock(Cache::class, function ($mock) {
            $mock->shouldReceive('fetch')->andReturn('thisIsACachedToken');
            $mock->shouldReceive('save')->andReturnUsing(function ($key, $token, $expire) {
                return $token;
            });
        });

        $accessToken->setCache($cache);
        $this->assertEquals($cache, $accessToken->getCache());

        $this->assertEquals('access_token', $accessToken->getQueryName());
        $this->assertArrayHasKey('access_token', $accessToken->getQueryFields());

        $accessToken->setQueryName('foo');

        $this->assertEquals('foo', $accessToken->getQueryName());
        $this->assertArrayHasKey('foo', $accessToken->getQueryFields());
        $this->assertArrayNotHasKey('access_token', $accessToken->getQueryFields());
    }
}
