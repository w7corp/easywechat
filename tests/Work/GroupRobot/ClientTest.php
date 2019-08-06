<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\GroupRobot;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\GroupRobot\Client;
use EasyWeChat\Work\GroupRobot\Messenger;

class ClientTest extends TestCase
{
    public function testMessage()
    {
        $client = $this->mockApiClient(Client::class);

        $this->assertInstanceOf(Messenger::class, $client->message('hello'));
    }

    public function testSend()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()
            ->httpPostJson('cgi-bin/webhook/send', ['foo' => 'bar'], ['key' => 'mock-key'])
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->send('mock-key', ['foo' => 'bar']));
    }
}
