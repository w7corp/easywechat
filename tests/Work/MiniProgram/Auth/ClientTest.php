<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\MiniProgram\Auth;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\MiniProgram\Auth\Client;

/**
 * Class Auth.
 *
 * @author Caikeal <caikeal@qq.com>
 */
class ClientTest extends TestCase
{
    public function testGetSessionKey()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpGet('cgi-bin/miniprogram/jscode2session', [
            'js_code' => 'js-code',
            'grant_type' => 'authorization_code',
        ])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->session('js-code'));
    }
}
