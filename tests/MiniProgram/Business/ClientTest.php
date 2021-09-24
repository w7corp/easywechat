<?php

/*
 * This file is part of the overtrue/wechat.
 *
 */

namespace EasyWeChat\Tests\MiniProgram\Business;

use EasyWeChat\MiniProgram\Business\Client;
use EasyWeChat\MiniProgram\Business\Messenger;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testRegister()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/business/register', [
            'account_name' => 'overtrue',
            'nickname' => '小超',
            'icon_media_id' => 'media_id',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->register('overtrue', '小超', 'media_id'));
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/business/update', [
            'business_id' => 1,
            'nickname' => '小小超',
            'icon_media_id' => 'test_id',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->update(1, '小小超', 'test_id'));
    }

    public function testGetBusiness()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/business/get', [
            'business_id' => 1,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getBusiness(1));
    }

    public function testList()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/business/list', [
            'offset' => 0,
            'count' => 10
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->list(0, 10));
    }

    public function testTyping()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/business/typing', [
            'business_id' => 1,
            'touser' => 'open-id',
            'command' => 'Typing',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->typing(1, 'open-id'));
    }

    public function testMessage()
    {
        $client = $this->mockApiClient(Client::class);

        $this->assertInstanceOf(Messenger::class, $client->message('text content'));
    }

    public function testSend()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/message/custom/business/send', ['foo' => 'bar', 'businessid' => 1])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->send(['foo' => 'bar', 'businessid' => 1]));
    }
}
