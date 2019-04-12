<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\Chat;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Chat\Client;

class ClientTest extends TestCase
{
    public function testGet()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpGet('cgi-bin/appchat/get', ['chatid' => 'overtrue'])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->get('overtrue'));
    }

    public function testCreate()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/appchat/create', ['foo' => 'bar'])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->create(['foo' => 'bar']));
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/appchat/update', ['chatid' => 'overtrue', 'foo' => 'bar'])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->update('overtrue', ['foo' => 'bar']));
    }

    public function testSend()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/appchat/send', ['foo' => 'bar'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->send(['foo' => 'bar']));
    }
}
