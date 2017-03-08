<?php

/**
 * Test AuthorizerTokenTest.php.
 *
 * @author lixiao <leonlx126@gmail.com>
 */

use EasyWeChat\OpenPlatform\Authorization;
use EasyWeChat\OpenPlatform\AuthorizerToken;

class AuthorizerTokenTest extends TestCase
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

    public function testGetTokenForced() {
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
        $mock = Mockery::mock(Authorization::class);
        $mock->shouldReceive('getAuthorizerAccessToken')
             ->andReturn($cachedToken);
        $mock->shouldReceive('handleAuthorizerAccessToken')
             ->andReturn($newToken);

        return new AuthorizerToken($appId, $mock);
    }
}