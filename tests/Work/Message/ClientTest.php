<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\Message;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Message\Client;
use EasyWeChat\Work\Message\Messenger;

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

        $client->expects()->httpPostJson('cgi-bin/message/send', ['foo' => 'bar'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->send(['foo' => 'bar']));
    }

    public function testUpdateTaskcard()
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'userids' => ['userid1','userid2'],
            'agentid' => 1,
            'task_id' => 'taskid1',
            'replace_name' => '已收到'
        ];

        $client->expects()->httpPostJson('cgi-bin/message/update_taskcard', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->updateTaskcard(['userid1','userid2'], 1, 'taskid1', '已收到'));
    }

    public function testTemplateCard()
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'userids' => ['userid1','userid2'],
            'agentid' => 1,
            'response_code' => 'testcode',
            'button' => [
                'replace_name' => '已收到'
            ]
        ];

        $client->expects()->httpPostJson('cgi-bin/message/update_template_card', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->updateTemplateCard(['userid1','userid2'], 1, 'testcode', '已收到'));
    }
}
