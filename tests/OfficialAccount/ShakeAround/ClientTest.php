<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\ShakeAround;

use EasyWeChat\OfficialAccount\ShakeAround\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testRegister()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('shakearound/account/register', ['foo' => 'bar'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->register(['foo' => 'bar']));
    }

    public function testStatus()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpGet('shakearound/account/auditstatus')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->status());
    }

    public function testUser()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('shakearound/user/getshakeinfo', ['ticket' => 'mock-ticket'])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->user('mock-ticket'));

        $client->expects()->httpPostJson('shakearound/user/getshakeinfo', ['ticket' => 'mock-ticket', 'need_poi' => 1])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->user('mock-ticket', true));

        $client->expects()->httpPostJson('shakearound/user/getshakeinfo', ['ticket' => 'mock-ticket', 'need_poi' => 1])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->userWithPoi('mock-ticket', true));
    }
}
