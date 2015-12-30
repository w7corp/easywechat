<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\Cache\Manager;
use EasyWeChat\Core\AccessToken;
use EasyWeChat\Core\Http;

class CoreAccessTokenTest extends PHPUnit_Framework_TestCase
{
    public function testGetToken()
    {
        $cache = Mockery::mock(Manager::class, function ($mock) {
            $mock->shouldReceive('get')->andReturn('thisIsACachedToken');
            $mock->shouldReceive('set')->andReturnUsing(function ($key, $token, $expire) {
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
        $cache = Mockery::mock(Manager::class, function ($mock) use ($cacheObj) {
            $mock->shouldReceive('get')->andReturnUsing(function ($cacheKey) {
                return;
            });

            $mock->shouldReceive('set')->andReturnUsing(function ($key, $token, $expire) use ($cacheObj) {
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
    }
}
