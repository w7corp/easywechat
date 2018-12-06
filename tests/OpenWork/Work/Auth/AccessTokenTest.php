<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenWork\Work\Auth;

use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OpenWork\Application;
use EasyWeChat\OpenWork\Work\Auth\AccessToken;
use EasyWeChat\Tests\TestCase;

class AccessTokenTest extends TestCase
{
    public function testGetCredentials()
    {
        $app = new ServiceContainer([
            'corp_id' => '1234',
            'secret' => 'secret',
        ]);
        $accessToken = \Mockery::mock(AccessToken::class, [$app, 'mock-auth-corp-id', 'mock-permanent-code', new Application()])->makePartial()->shouldAllowMockingProtectedMethods();

        $this->assertSame([
            'auth_corpid' => 'mock-auth-corp-id',
            'permanent_code' => 'mock-permanent-code',
        ], $accessToken->getCredentials());
    }

    public function testEndpoint()
    {
        $app = \Mockery::mock(new ServiceContainer());

        $openWork = new Application();

        $openWork['suite_access_token'] = \Mockery::mock(\EasyWeChat\OpenWork\SuiteAuth\AccessToken::class, function ($mock) {
            $mock->shouldReceive('getToken')->andReturn([
                'suite_access_token' => 'mock-suit-access-token',
            ]);
        });

        $accessToken = \Mockery::mock(AccessToken::class, [$app, 'mock-auth-corp-id', 'mock-permanent-code', $openWork])->makePartial()->shouldAllowMockingProtectedMethods();

        $this->assertSame('cgi-bin/service/get_corp_token?suite_access_token=mock-suit-access-token', $accessToken->getEndpoint());
    }
}
