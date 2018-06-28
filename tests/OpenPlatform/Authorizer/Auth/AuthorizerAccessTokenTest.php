<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenPlatform\Authorizer\Auth;

use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OpenPlatform\Application;
use EasyWeChat\OpenPlatform\Auth\AccessToken;
use EasyWeChat\OpenPlatform\Authorizer\Auth\AccessToken as AuthorizerAccessToken;
use EasyWeChat\Tests\TestCase;

class AuthorizerAccessTokenTest extends TestCase
{
    public function testGetCredentials()
    {
        $app = new ServiceContainer([
            'app_id' => 'mock-app-id',
            'refresh_token' => 'mock-refresh-token',
        ]);
        $openPlatform = new Application([
            'app_id' => 'component-app-id',
            'secret' => 'component-secret',
        ]);
        $token = \Mockery::mock(AuthorizerAccessToken::class, [$app, $openPlatform])->makePartial()->shouldAllowMockingProtectedMethods();

        $this->assertSame([
            'component_appid' => 'component-app-id',
            'authorizer_appid' => 'mock-app-id',
            'authorizer_refresh_token' => 'mock-refresh-token',
        ], $token->getCredentials());
    }

    public function testGetEndpoint()
    {
        $openPlatform = new Application();

        $openPlatform['access_token'] = \Mockery::mock(AccessToken::class, function ($mock) {
            $mock->shouldReceive('getToken')->andReturn([
                'component_access_token' => 'foobar',
            ]);
        });

        $this->assertSame(
            'cgi-bin/component/api_authorizer_token?component_access_token=foobar',
            (new AuthorizerAccessToken(\Mockery::mock(ServiceContainer::class), $openPlatform))->getEndpoint()
        );
    }
}
