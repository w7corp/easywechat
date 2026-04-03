<?php

namespace EasyWeChat\Tests\OpenPlatform;

use EasyWeChat\OpenPlatform\Authorization;
use EasyWeChat\OpenPlatform\AuthorizerAccessToken;
use EasyWeChat\Tests\TestCase;

class AuthorizationTest extends TestCase
{
    public function test_get_app_id()
    {
        $authorization = new Authorization([
            'authorization_info' => [
                'authorizer_appid' => 'mock-app-id',
            ],
        ]);

        $this->assertSame('mock-app-id', $authorization->getAppId());
    }

    public function test_get_access_token()
    {
        $authorization = new Authorization([
            'authorization_info' => [
                'authorizer_appid' => 'mock-app-id',
                'authorizer_access_token' => 'mock-access-token',
            ],
        ]);

        $this->assertInstanceOf(AuthorizerAccessToken::class, $authorization->getAccessToken());
        $this->assertSame('mock-app-id', $authorization->getAccessToken()->getAppId());
        $this->assertSame('mock-access-token', $authorization->getAccessToken()->getToken());
    }

    public function test_get_refresh_token()
    {
        $authorization = new Authorization([
            'authorization_info' => [
                'authorizer_appid' => 'mock-app-id',
                'authorizer_refresh_token' => 'mock-refresh-token',
            ],
        ]);

        $this->assertSame('mock-refresh-token', $authorization->getRefreshToken());
    }

    public function test_missing_keys_return_empty_values_without_warnings()
    {
        $authorization = new Authorization([]);

        $errors = [];
        $handler = static function (int $severity, string $message) use (&$errors): bool {
            $errors[] = [$severity, $message];

            return true;
        };

        \set_error_handler($handler);

        try {
            $accessToken = $authorization->getAccessToken();

            $this->assertSame('', $authorization->getAppId());
            $this->assertSame('', $accessToken->getAppId());
            $this->assertSame('', $accessToken->getToken());
            $this->assertSame('', $authorization->getRefreshToken());
        } finally {
            \restore_error_handler();
        }

        $this->assertSame([], $errors);
    }
}
