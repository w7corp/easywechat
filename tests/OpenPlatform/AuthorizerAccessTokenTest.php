<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenPlatform;

use EasyWeChat\Applications\OpenPlatform\Core\AuthorizerAccessToken;
use EasyWeChat\Support\Collection;
use EasyWeChat\Tests\TestCase;
use Mockery as m;

class AuthorizerAccessTokenTest extends TestCase
{
    public function testGetAppidAndOpenplatformAppid()
    {
        $instance = $this->make('appid@123');

        $this->assertSame('appid@123', $instance->getAppId());
        $this->assertSame('open-platform-appid', $instance->getClientId());
    }

    public function testGetToken()
    {
        $auth = $this->make('appid@123', 'token@123');

        $this->assertSame('token@123', $auth->getToken());
    }

    public function testGetTokenExpired()
    {
        $auth = $this->make('appid@123', null, 'token@456');

        $this->assertSame('token@456', $auth->getToken());
    }

    public function testGetTokenForced()
    {
        $auth = $this->make('appid@123', 'token@123', 'token@456');

        $this->assertSame('token@456', $auth->getToken(true));
    }

    public function testGetCacheKey()
    {
        $instance = $this->make('appid@123', 'token@123', 'token@456');

        $this->assertSame('easywechat.open_platform.authorizer_access_token.open-platform-appidappid@123', $instance->getCacheKey());
    }

    private function make($appId, $cachedToken = null, $newToken = null)
    {
        $cache = m::mock('Doctrine\Common\Cache\Cache', function ($mock) use ($cachedToken) {
            $mock->shouldReceive('fetch')->andReturn($cachedToken);
            $mock->shouldReceive('save')->andReturnUsing(function ($key, $token, $expire) {
                return $token;
            });
        });

        $baseApi = m::mock('EasyWeChat\Applications\OpenPlatform\Api\BaseApi', function ($mock) use ($newToken) {
            $mock->shouldReceive('getAuthorizerToken')->andReturn(
                new Collection(['authorizer_access_token' => $newToken, 'expires_in' => 7200])
            );
        });

        return (new AuthorizerAccessToken('open-platform-appid'))
            ->setApi($baseApi)
            ->setCache($cache)
            ->setAppId($appId)
            ->setRefreshToken('authorizer-refresh-token');
    }
}
