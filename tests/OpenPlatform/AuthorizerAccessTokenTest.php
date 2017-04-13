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
        $appId = 'appid@123';
        $cachedToken = 'token@123';
        $auth = $this->make($appId, $cachedToken, null);

        $token = $auth->getToken();
        $this->assertEquals($cachedToken, $token);
    }

    public function testGetTokenExpired()
    {
        $appId = 'appid@123';
        $newToken = 'token@456';
        $auth = $this->make($appId, null, $newToken);

        $token = $auth->getToken();
        $this->assertEquals($newToken, $token);
    }

    public function testGetTokenForced()
    {
        $appId = 'appid@123';
        $cachedToken = 'token@123';
        $newToken = 'token@456';
        $auth = $this->make($appId, $cachedToken, $newToken);

        $token = $auth->getToken(true);
        $this->assertEquals($newToken, $token);
    }

    private function make($appId, $cachedToken, $newToken)
    {
        /** @var Authorization|\Mockery\MockInterface $mock */
        $mock = \Mockery::mock('EasyWeChat\OpenPlatform\Authorization');
        $mock->shouldReceive('getAuthorizerAppId')->andReturn($appId);
        $mock->shouldReceive('getAuthorizerRefreshToken')->andReturn($newToken);
        $mock->shouldReceive('setAuthorizerAccessToken')->andReturn(true);
        $mock->shouldReceive('getAuthorizerAccessToken')
             ->andReturn($cachedToken);
        $mock->shouldReceive('getApi')
             ->andReturn(\Mockery::mock('EasyWeChat\OpenPlatform\Api\BaseApi', function ($mock) use ($newToken) {
                 $mock->shouldReceive('getAuthorizerToken')->andReturn(new Collection(['authorizer_access_token' => $newToken, 'expires_in' => 7200]));
             }));

        return new AuthorizerAccessToken($appId, $mock);
    }
}
