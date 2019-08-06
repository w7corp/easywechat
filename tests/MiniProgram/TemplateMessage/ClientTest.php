<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MiniProgram\TemplateMessage;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\MiniProgram\TemplateMessage\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testSend()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        // without touser
        try {
            $client->send();
        } catch (\Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame('Attribute "touser" can not be empty!', $e->getMessage());
        }

        // without template_id
        try {
            $client->send(['touser' => 'mock-openid']);
        } catch (\Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame('Attribute "template_id" can not be empty!', $e->getMessage());
        }

        $client->expects()->httpPostJson('cgi-bin/message/wxopen/template/send', [
            'touser' => 'mock-openid',
            'template_id' => 'mock-template_id',
            'page' => '',
            'form_id' => 'mock-form_id',
            'emphasis_keyword' => '',
            'data' => [],
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->send(['touser' => 'mock-openid', 'template_id' => 'mock-template_id', 'form_id' => 'mock-form_id']));
    }

    public function testList()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/wxopen/template/library/list', ['offset' => 5, 'count' => 10])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->list(5, 10));
    }

    public function testGet()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/wxopen/template/library/get', ['id' => 'A123'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->get('A123'));
    }

    public function testAdd()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/wxopen/template/add', ['id' => 'A123', 'keyword_id_list' => ['foo', 'bar']])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->add('A123', ['foo', 'bar']));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/wxopen/template/del', ['template_id' => 'A123'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->delete('A123'));
    }

    public function testGetTemplates()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/wxopen/template/list', ['offset' => 5, 'count' => 10])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getTemplates(5, 10));
    }
}
