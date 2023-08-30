<?php

namespace EasyWeChat\Tests\MiniApp;

use EasyWeChat\MiniApp\AccessToken;
use EasyWeChat\Tests\TestCase;

class AccessTokenTest extends TestCase
{
    public function test_it_will_use_mini_app_cache_prefix()
    {
        $accessToken = new AccessToken('mock-app-id', 'mock-secret');

        $this->assertStringStartsWith('mini_app.access_token', $accessToken->getKey());
    }
}
