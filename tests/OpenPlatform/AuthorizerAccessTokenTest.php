<?php

/**
 * Test AuthorizerAccessTokenTest.php.
 *
 * @author lixiao <leonlx126@gmail.com>
 */

namespace EasyWeChat\Tests\OpenPlatform;

use EasyWeChat\OpenPlatform\AuthorizerAccessToken;
use EasyWeChat\Support\Collection;
use EasyWeChat\Tests\TestCase;

class AuthorizerAccessTokenTest extends TestCase
{
    public function testGetToken()
    {
        $auth = $this->make('appid@123', 'token@123', null);

        $this->assertEquals('token@123', $auth->getToken());
    }

    public function testGetTokenExpired()
    {
        $auth = $this->make('appid@123', null, 'token@456');

        $this->assertEquals('token@456', $auth->getToken());
    }

    public function testGetTokenForced()
    {
        $auth = $this->make('appid@123', 'token@123', 'token@456');

        $this->assertEquals('token@456', $auth->getToken(true));
    }

    private function make($appId, $cachedToken, $newToken)
    {
        /** @var \EasyWeChat\OpenPlatform\Authorizer|\Mockery\MockInterface $mock */
        $mock = \Mockery::mock('EasyWeChat\OpenPlatform\Authorizer');
        $mock->shouldReceive('getAppId')->andReturn($appId);
        $mock->shouldReceive('getRefreshToken')->andReturn($newToken);
        $mock->shouldReceive('setAccessToken')->andReturn(true);
        $mock->shouldReceive('getAccessToken')
             ->andReturn($cachedToken);
        $mock->shouldReceive('getApi')
             ->andReturn(\Mockery::mock('EasyWeChat\OpenPlatform\Api\BaseApi', function ($mock) use ($newToken) {
                 $mock->shouldReceive('getAuthorizerToken')->andReturn(new Collection(['authorizer_access_token' => $newToken, 'expires_in' => 7200]));
             }));

        return new AuthorizerAccessToken($appId, $mock);
    }
}
