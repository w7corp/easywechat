<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\Kf;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Kf\MessageClient;

/**
 * Class MessageClientTest.
 *
 * @package EasyWeChat\Tests\Work\Kf
 *
 * @author 读心印 <aa24615@qq.com>
 */

class MessageClientTest extends TestCase
{
    public function testState()
    {
        $client = $this->mockApiClient(MessageClient::class);
        $client->expects()->httpPostJson('cgi-bin/kf/service_state/get', [
            'open_kfid' => 'kfxxxxxxxxxxxxxx',
            'external_userid' => 'wmxxxxx123'
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->state('kfxxxxxxxxxxxxxx', 'wmxxxxx123'));
    }

    public function testUpdateState()
    {
        $client = $this->mockApiClient(MessageClient::class);
        $client->expects()->httpPostJson('cgi-bin/kf/service_state/trans', [
            'open_kfid' => 'kfxxxxxxxxxxxxxx',
            'external_userid' => 'wmxxxxx123',
            'service_state' => 3,
            'servicer_userid' => 'zhangsan'
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->updateState('kfxxxxxxxxxxxxxx', 'wmxxxxx123', 3, 'zhangsan'));
    }


    public function testSync()
    {
        $client = $this->mockApiClient(MessageClient::class);
        $client->expects()->httpPostJson('cgi-bin/kf/sync_msg', [
            'cursor' => '123',
            'token' => 'token_test',
            'limit' => 1000
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->sync('123', 'token_test', 1000));
    }

    public function testSend()
    {
        $client = $this->mockApiClient(MessageClient::class);

        $params = [
            'touser' => 'EXTERNAL_USERID',
            'open_kfid' => 'OPEN_KFID',
            'msgid' => 'MSGID',
            'msgtype' => 'text',
            'text' => [
                'content' => '你购买的物品已发货，可点击链接查看物流状态http://work.weixin.qq.com/xxxxxx'
            ]
        ];

        $client->expects()->httpPostJson('cgi-bin/kf/send_msg', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->send($params));
    }

    public function testEvent()
    {
        $client = $this->mockApiClient(MessageClient::class);

        $params = [
            'touser' => 'EXTERNAL_USERID',
            'open_kfid' => 'OPEN_KFID',
            'msgid' => 'MSGID',
            'msgtype' => 'text',
            'text' => [
                'content' => '欢迎咨询'
            ]
        ];

        $client->expects()->httpPostJson('cgi-bin/kf/send_msg_on_event', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->event($params));
    }
}
