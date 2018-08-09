<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\TemplateMessage;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\OfficialAccount\TemplateMessage\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testSetIndustry()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/template/api_set_industry', [
            'industry_id1' => 'foo',
            'industry_id2' => 'bar',
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->setIndustry('foo', 'bar'));
    }

    public function testGetIndustry()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/template/get_industry')->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->getIndustry());
    }

    public function testAddTemplate()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/template/api_add_template', [
            'template_id_short' => 'mock-id',
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->addTemplate('mock-id'));
    }

    public function testGetPrivateTemplates()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/template/get_all_private_template')->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->getPrivateTemplates());
    }

    public function testDeletePrivateTemplate()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/template/del_private_template', [
            'template_id' => 'mock-id',
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->deletePrivateTemplate('mock-id'));
    }

    /**
     * Test send().
     */
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

        $client->expects()->httpPostJson('cgi-bin/message/template/send', [
            'touser' => 'mock-openid',
            'template_id' => 'mock-template_id',
            'url' => '',
            'data' => [],
            'miniprogram' => '',
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->send(['touser' => 'mock-openid', 'template_id' => 'mock-template_id']));

        // with miniprogram
        $client->expects()->httpPostJson('cgi-bin/message/template/send', [
            'touser' => 'mock-openid',
            'template_id' => 'mock-template_id',
            'url' => '',
            'data' => [],
            'miniprogram' => [
                'appid' => 'id',
                'pagepath' => 'path',
            ],
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->send(['touser' => 'mock-openid', 'template_id' => 'mock-template_id', 'miniprogram' => ['appid' => 'id', 'pagepath' => 'path']]));
    }

    public function testFormatData()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        $this->assertSame([
            'foo' => ['value' => 'string'],
            'bar' => ['value' => 'content', 'color' => '#F00'],
            'baz' => ['value' => 'hello', 'color' => '#550038'],
            'zoo' => ['value' => 'hello'],
        ], $client->formatData([
            'foo' => 'string',
            'bar' => ['content', '#F00'],
            'baz' => ['value' => 'hello', 'color' => '#550038'],
            'zoo' => ['value' => 'hello'],
        ]));
    }

    public function testSendSubscription()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        try {
            $client->sendSubscription();
        } catch (\Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame('Attribute "touser" can not be empty!', $e->getMessage());
        }

        $client->expects()->httpPostJson('cgi-bin/message/template/subscribe', [
            'touser' => 'mock-openid',
            'template_id' => 'mock-template_id',
            'url' => '',
            'data' => [],
            'miniprogram' => '',
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->sendSubscription(['touser' => 'mock-openid', 'template_id' => 'mock-template_id']));

        $client->expects()->httpPostJson('cgi-bin/message/template/subscribe', [
                'touser' => 'foo',
                'template_id' => 'bar',
                'url' => 'https://easywechat.org',
                'scene' => 1000,
                'title' => 'title',
                'data' => [
                    'content' => ['value' => 'VALUE'],
                ],
            'miniprogram' => '',
        ])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->sendSubscription([
            'touser' => 'foo',
            'template_id' => 'bar',
            'url' => 'https://easywechat.org',
            'scene' => 1000,
            'title' => 'title',
            'data' => [
                'content' => 'VALUE',
            ],
        ]));
    }
}
