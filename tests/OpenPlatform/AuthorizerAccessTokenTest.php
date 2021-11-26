<?php

namespace EasyWeChat\Tests\OpenPlatform;

use EasyWeChat\OpenPlatform\AuthorizerAccessToken;
use EasyWeChat\Tests\TestCase;

class AuthorizerAccessTokenTest extends TestCase
{
    public function test_get_app_id_and_token()
    {
        $token = new AuthorizerAccessToken('mock-app-id', 'mock-access-token');

        $this->assertSame('mock-app-id', $token->getAppId());
        $this->assertSame('mock-access-token', $token->getToken());
        $this->assertSame('mock-access-token', \strval($token));
        $this->assertSame([
            'access_token' => 'mock-access-token',
        ], $token->toQuery());
    }
}
