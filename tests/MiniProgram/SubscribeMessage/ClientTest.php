<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MiniProgram\SubscribeMessage;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\MiniProgram\SubscribeMessage\Client;
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

        // without data
        try {
            $client->send(['touser' => 'mock-openid', 'template_id' => 'mock-template_id']);
        } catch (\Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame('Attribute "data" can not be empty!', $e->getMessage());
        }

        // format message
        $this->assertSame([
            'touser' => 'mock-openid',
            'template_id' => 'mock-template_id',
            'page' => '',
            'data' => ['thing1' => ['value' => 'thing1.DATA']],
        ], $client->formatMessage([
            'touser' => 'mock-openid',
            'template_id' => 'mock-template_id',
            'page' => '',
            'data' => ['thing1' => 'thing1.DATA'],
        ]));

        $this->assertSame([
            'touser' => 'mock-openid',
            'template_id' => 'mock-template_id',
            'page' => '',
            'data' => ['thing1' => ['value' => 'thing1.DATA']],
        ], $client->formatMessage([
            'touser' => 'mock-openid',
            'template_id' => 'mock-template_id',
            'page' => '',
            'data' => ['thing1' => ['thing1.DATA']],
        ]));

        $this->assertSame([
            'touser' => 'mock-openid',
            'template_id' => 'mock-template_id',
            'page' => '',
            'data' => ['thing1' => ['value' => 'thing1.DATA']],
        ], $client->formatMessage([
            'touser' => 'mock-openid',
            'template_id' => 'mock-template_id',
            'page' => '',
            'data' => ['thing1' => ['thing1.DATA', 'ignore value']],
        ]));

        $this->assertSame([
            'touser' => 'mock-openid',
            'template_id' => 'mock-template_id',
            'page' => '',
            'data' => ['thing1' => ['value' => 'thing1.DATA']],
        ], $client->formatMessage([
            'touser' => 'mock-openid',
            'template_id' => 'mock-template_id',
            'page' => '',
            'data' => ['thing1' => ['value' => 'thing1.DATA']],
        ]));

        $this->assertSame([
            'touser' => 'mock-openid',
            'template_id' => 'mock-template_id',
            'page' => '',
            'data' => ['thing1' => ['value' => 'thing1.DATA']],
        ], $client->formatMessage([
            'touser' => 'mock-openid',
            'template_id' => 'mock-template_id',
            'page' => '',
            'data' => ['thing1' => ['value' => 'thing1.DATA', 'color' => 'ignore value']],
        ]));

        $client->expects()->httpPostJson('cgi-bin/message/subscribe/send', [
            'touser' => 'mock-openid',
            'template_id' => 'mock-template_id',
            'page' => '',
            'data' => ['thing1' => ['value' => 'thing1.DATA']],
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->send(['touser' => 'mock-openid', 'template_id' => 'mock-template_id', 'data' => ['thing1' => 'thing1.DATA']]));
    }

    public function testAddTemplate()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        $client->expects()->httpPost('wxaapi/newtmpl/addtemplate', [
            'tid' => 'mock-tid',
            'kidList' => ['mock-kid'],
            'sceneDesc' => 'mock-scene',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->addTemplate('mock-tid', ['mock-kid'], 'mock-scene'));
    }

    public function testDeleteTemplate()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        $client->expects()->httpPost('wxaapi/newtmpl/deltemplate', [
            'priTmplId' => 'mock-template-id',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->deleteTemplate('mock-template-id'));
    }

    public function testGetTemplateKeywords()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        $client->expects()->httpGet('wxaapi/newtmpl/getpubtemplatekeywords', [
            'tid' => 'mock-tid',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getTemplateKeywords('mock-tid'));
    }

    public function testGetTemplateTitles()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        $client->expects()->httpGet('wxaapi/newtmpl/getpubtemplatetitles', [
            'ids' => 'mock-tid1,mock-tid2',
            'start' => 0,
            'limit' => 30,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getTemplateTitles(['mock-tid1', 'mock-tid2']));
    }

    public function testGetTemplates()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        $client->expects()->httpGet('wxaapi/newtmpl/gettemplate')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getTemplates());
    }

    public function testGetCategory()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        $client->expects()->httpGet('wxaapi/newtmpl/getcategory')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getCategory());
    }
}
