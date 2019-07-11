<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenWork\MiniProgram;

use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OpenWork\MiniProgram\Client;
use EasyWeChat\OpenWork\SuiteAuth\AccessToken;
use EasyWeChat\Tests\TestCase;

/**
 * Class Auth.
 */
class ClientTest extends TestCase
{
    public function testGetSessionKey()
    {
        $app = new ServiceContainer(['suite_id' => 'mock-suit-id']);
        $app['suite_access_token'] = \Mockery::mock(AccessToken::class);
        $client = $this->mockApiClient(Client::class, [], $app);

        $client->expects()->httpGet('cgi-bin/service/miniprogram/jscode2session', [
            'js_code' => 'js-code',
            'grant_type' => 'authorization_code',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->session('js-code'));
    }
}
