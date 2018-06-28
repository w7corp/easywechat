<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenPlatform\Authorizer\MiniProgram\Auth;

use EasyWeChat\OpenPlatform\Application;
use EasyWeChat\OpenPlatform\Auth\AccessToken;
use EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Auth\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testSession()
    {
        $app = new Application(['app_id' => 'component-app-id']);
        $app['access_token'] = \Mockery::mock(AccessToken::class, function ($mock) {
            $mock->expects()->getToken()->andReturn(['component_access_token' => 'foobar']);
        });

        $client = \Mockery::mock(Client::class.'[httpGet]', [new Application(['app_id' => 'app-id']), $app]);

        $client->expects()->httpGet('sns/component/jscode2session', [
            'appid' => 'app-id',
            'js_code' => 'js-code',
            'grant_type' => 'authorization_code',
            'component_appid' => 'component-app-id',
            'component_access_token' => 'foobar',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->session('js-code'));
    }
}
