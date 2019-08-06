<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\ExternalContact;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\ExternalContact\MessageClient;

class MessageTest extends TestCase
{
    public function testSubmit()
    {
        $client = $this->mockApiClient(MessageClient::class);

        $client->shouldReceive('httpPostJson')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->submit([
            'external_userid' => [
                'mock-userid-1',
                'mock-userid-2',
            ],
            'sender' => 'zhangsan',
            'text' => [
                'content' => 'mock-content',
            ],
            'image' => [
                'media_id' => 'mock-media_id',
            ],
            'link' => [
                'title' => 'mock-title',
                'picurl' => 'mock-picurl',
                'desc' => 'mock-desc',
                'url' => 'mock-url',
            ],
            'miniprogram' => [
                'title' => 'mock-title',
                'pic_media_id' => 'mock-pic_media_id',
                'appid' => 'mock-appid',
                'page' => 'mock-page',
            ],
        ]));
    }

    public function testSubmitWithoutTextContent()
    {
        $client = $this->mockApiClient(MessageClient::class);
        $client->shouldReceive('httpPostJson');

        try {
            $client->submit([
                'text' => [
                    'test',
                ],
            ]);
        } catch (\Exception $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertSame('Attribute "content" can not be empty!', $exception->getMessage());
        }
    }

    public function testSubmitWithoutImageMediaId()
    {
        $client = $this->mockApiClient(MessageClient::class);
        $client->shouldReceive('httpPostJson');

        try {
            $client->submit([
                'image' => [
                    'test',
                ],
            ]);
        } catch (\Exception $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertSame('Attribute "media_id" can not be empty!', $exception->getMessage());
        }
    }

    public function testSubmitWithoutLinkField()
    {
        $client = $this->mockApiClient(MessageClient::class);
        $client->shouldReceive('httpPostJson');

        try {
            $client->submit([
                'link' => [
                    'test',
                ],
            ]);
        } catch (\Exception $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertSame('Attribute "title" can not be empty!', $exception->getMessage());
        }

        try {
            $client->submit([
                'link' => [
                    'title' => 'mock-title',
                ],
            ]);
        } catch (\Exception $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertSame('Attribute "url" can not be empty!', $exception->getMessage());
        }
    }

    public function testSubmitWithoutMiniprogramField()
    {
        $client = $this->mockApiClient(MessageClient::class);
        $client->shouldReceive('httpPostJson')->andReturn('mock-result');

        try {
            $client->submit([
                'miniprogram' => [
                    'test',
                ],
            ]);
        } catch (\Exception $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertSame('Attribute "title" can not be empty!', $exception->getMessage());
        }

        try {
            $client->submit([
                'miniprogram' => [
                    'title' => 'mock-title',
                ],
            ]);
        } catch (\Exception $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertSame('Attribute "pic_media_id" can not be empty!', $exception->getMessage());
        }

        try {
            $client->submit([
                'miniprogram' => [
                    'title' => 'mock-title',
                    'pic_media_id' => 'mock-pic_media_id',
                ],
            ]);
        } catch (\Exception $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertSame('Attribute "appid" can not be empty!', $exception->getMessage());
        }

        try {
            $client->submit([
                'miniprogram' => [
                    'title' => 'mock-title',
                    'pic_media_id' => 'mock-pic_media_id',
                    'appid' => 'mock-appid',
                ],
            ]);
        } catch (\Exception $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertSame('Attribute "page" can not be empty!', $exception->getMessage());
        }

        $this->assertSame('mock-result', $client->submit([
            'miniprogram' => [
                'title' => 'mock-title',
                'pic_media_id' => 'mock-pic_media_id',
                'appid' => 'mock-appid',
                'page' => 'mock-page',
            ],
        ]));
    }

    public function testGetGroupMsgResult()
    {
        $client = $this->mockApiClient(MessageClient::class);

        $client->expects()->httpPostJson('cgi-bin/externalcontact/get_group_msg_result', [
            'msgid' => 'mock-msgid',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->get('mock-msgid'));
    }

    public function testSendWelcome()
    {
        $client = $this->mockApiClient(MessageClient::class);

        $params = [
            'welcome_code' => 'mock-welcome-code',
            'text' => [
                'content' => 'mock-content',
            ],
        ];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/send_welcome_msg', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->sendWelcome('mock-welcome-code', [
            'text' => [
                'content' => 'mock-content',
            ],
        ]));
    }
}
