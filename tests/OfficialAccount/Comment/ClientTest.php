<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Comment;

use EasyWeChat\OfficialAccount\Comment\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testOpen()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/comment/open', [
            'msg_data_id' => 'mock-id',
            'index' => 1,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->open('mock-id', 1));
    }

    public function testClose()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/comment/close', [
            'msg_data_id' => 'mock-id',
            'index' => 2,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->close('mock-id', 2));
    }

    public function testList()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/comment/list', [
            'msg_data_id' => 'mock-id',
            'index' => 3,
            'begin' => 0,
            'count' => 20,
            'type' => 1,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->list('mock-id', 3, 0, 20, 1));
    }

    public function testMarkElect()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/comment/markelect', [
            'msg_data_id' => 'mock-id',
            'index' => 3,
            'user_comment_id' => 18,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->markElect('mock-id', 3, 18));
    }

    public function testUnmarkElect()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/comment/unmarkelect', [
            'msg_data_id' => 'mock-id',
            'index' => 3,
            'user_comment_id' => 18,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->unmarkElect('mock-id', 3, 18));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/comment/delete', [
            'msg_data_id' => 'mock-id',
            'index' => 3,
            'user_comment_id' => 18,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->delete('mock-id', 3, 18));
    }

    public function testReply()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/comment/reply/add', [
            'msg_data_id' => 'mock-id',
            'index' => 3,
            'user_comment_id' => 18,
            'content' => 'mock-content',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->reply('mock-id', 3, 18, 'mock-content'));
    }

    public function testDeleteReply()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/comment/reply/delete', [
            'msg_data_id' => 'mock-id',
            'index' => 3,
            'user_comment_id' => 18,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->deleteReply('mock-id', 3, 18));
    }
}
