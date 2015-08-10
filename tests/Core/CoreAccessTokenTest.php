<?php

use EasyWeChat\Cache\Manager;
use EasyWeChat\Core\AccessToken;
use EasyWeChat\Core\Http;

class CoreAccessTokenTest extends TestCase
{
    public function testGetToken()
    {
        $cache = Mockery::mock(Manager::class, function ($mock) {
            $mock->shouldReceive('get')->andReturn('thisIsACachedToken');
        });

        $http = Mockery::mock(Http::class);

        // cached
        $accessToken = new AccessToken('appId', 'secret', $cache, $http);
        $this->assertEquals('thisIsACachedToken', $accessToken->getToken());
    }

    /**
     * Test getToken() without cache.
     */
    public function testNonCachedGetToken()
    {
        $cacheObj = new stdClass();

        // non-cached
        $cache = Mockery::mock(Manager::class, function ($mock) use ($cacheObj) {
            $mock->shouldReceive('get')->andReturnUsing(function ($cacheKey, $callback) {
                return $callback($cacheKey);
            });

            $mock->shouldReceive('set')->andReturnUsing(function ($key, $token, $expire) use ($cacheObj) {
                $cacheObj->cacheKey = $key;
                $cacheObj->token = $token;
                $cacheObj->expire = $expire;

                return $token;
            });
        });

        $http = Mockery::mock(Http::class, function ($mock) {
            $mock->shouldReceive('get')->andReturn([
                    'access_token' => 'thisIsAToken',
                    'expires_in' => 7200,
                ]);
        });

        $accessToken = new AccessToken('appId', 'secret', $cache, $http);
        $this->assertEquals('thisIsAToken', $accessToken->getToken());
        $this->assertEquals('thisIsAToken', $cacheObj->token);
        $this->assertEquals(7100, $cacheObj->expire);
    }
}
