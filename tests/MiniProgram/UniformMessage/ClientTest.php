<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MiniProgram\UniformMessage;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\MiniProgram\UniformMessage\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testFormatMessage()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        $this->assertSame([
            'touser' => 'mock-touser',
            'mp_template_msg' => [
                'appid' => 'mock-appid',
                'template_id' => 'mock-template-id',
                'template_id' => 'mock-template-id',
                'url' => 'mock-url',
                'miniprogram' => [
                    'appid' => 'mock-mini-program-appid',
                    'pagepath' => 'mock-page-path',
                ],
                'data' => [
                    'foo' => ['value' => 'string'],
                    'bar' => ['value' => 'content', 'color' => '#F00'],
                    'baz' => ['value' => 'hello', 'color' => '#550038'],
                    'zoo' => ['value' => 'hello'],
                ],
            ],
            'weapp_template_msg' => [
                'template_id' => 'mock-template-id',
                'page' => 'mock-page',
                'form_id' => 'mock-form-id',
                'data' => [
                    'foo' => ['value' => 'string'],
                    'bar' => ['value' => 'content', 'color' => '#F00'],
                    'baz' => ['value' => 'hello', 'color' => '#550038'],
                    'zoo' => ['value' => 'hello'],
                ],
                'emphasis_keyword' => '',
            ],
        ], $client->formatMessage([
            'touser' => 'mock-touser',
            'mp_template_msg' => [
                'appid' => 'mock-appid',
                'template_id' => 'mock-template-id',
                'url' => 'mock-url',
                'miniprogram' => [
                    'appid' => 'mock-mini-program-appid',
                    'pagepath' => 'mock-page-path',
                ],
                'data' => [
                    'foo' => 'string',
                    'bar' => ['content', '#F00'],
                    'baz' => ['value' => 'hello', 'color' => '#550038'],
                    'zoo' => ['value' => 'hello'],
                ],
            ],
            'weapp_template_msg' => [
                'template_id' => 'mock-template-id',
                'page' => 'mock-page',
                'form_id' => 'mock-form-id',
                'data' => [
                    'foo' => 'string',
                    'bar' => ['content', '#F00'],
                    'baz' => ['value' => 'hello', 'color' => '#550038'],
                    'zoo' => ['value' => 'hello'],
                ],
            ],
        ]));

        try {
            $client->formatMessage([
                'touser' => '',
                'mp_template_msg' => [],
            ]);
        } catch (\Exception $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertSame('Attribute "touser" can not be empty!', $exception->getMessage());
        }

        $this->assertSame([
            'touser' => 'mock-user',
            'mp_template_msg' => [],
        ], $client->formatMessage([
            'touser' => 'mock-user',
            'mp_template_msg' => [],
        ]));

        $this->assertSame([
            'touser' => 'mock-user',
            'weapp_template_msg' => [],
        ], $client->formatMessage([
            'touser' => 'mock-user',
            'weapp_template_msg' => [],
        ]));
    }

    public function testFormatMpMessage()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        $this->assertSame([
            'appid' => 'mock-appid',
            'template_id' => 'mock-template-id',
            'url' => 'mock-url',
            'miniprogram' => [
                'appid' => 'mock-mini-program-appid',
                'pagepath' => 'mock-page-path',
            ],
            'data' => [
                'foo' => ['value' => 'string'],
                'bar' => ['value' => 'content', 'color' => '#F00'],
                'baz' => ['value' => 'hello', 'color' => '#550038'],
                'zoo' => ['value' => 'hello'],
            ],
        ], $client->formatMpMessage([
            'appid' => 'mock-appid',
            'template_id' => 'mock-template-id',
            'url' => 'mock-url',
            'miniprogram' => [
                'appid' => 'mock-mini-program-appid',
                'pagepath' => 'mock-page-path',
            ],
            'data' => [
                'foo' => 'string',
                'bar' => ['content', '#F00'],
                'baz' => ['value' => 'hello', 'color' => '#550038'],
                'zoo' => ['value' => 'hello'],
            ],
        ]));

        // miss appid
        try {
            $client->formatMpMessage([
                'appid' => '',
                'template_id' => 'mock-template-id',
                'url' => 'mock-url',
                'miniprogram' => [
                    'appid' => 'mock-mini-program-appid',
                    'pagepath' => 'mock-page-path',
                ],
                'data' => [],
            ]);
        } catch (\Exception $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertSame('Attribute "appid" can not be empty!', $exception->getMessage());
        }

        // miss template_id
        try {
            $client->formatMpMessage([
                'appid' => 'mock-appid',
                'template_id' => '',
                'url' => 'mock-url',
                'miniprogram' => [
                    'appid' => 'mock-mini-program-appid',
                    'pagepath' => 'mock-page-path',
                ],
                'data' => [],
            ]);
        } catch (\Exception $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertSame('Attribute "template_id" can not be empty!', $exception->getMessage());
        }

        // miss miniprogram
        try {
            $client->formatMpMessage([
                'appid' => 'mock-appid',
                'template_id' => 'mock-template-id',
                'url' => 'mock-url',
                'miniprogram' => '',
                'data' => [],
            ]);
        } catch (\Exception $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertSame('Attribute "miniprogram" can not be empty!', $exception->getMessage());
        }
    }

    public function testFormatMpMessageWithMissMiniProgramAppid()
    {
        $config = [
            'app_id' => 'mock-appid',
        ];
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer($config))->makePartial();

        $this->assertSame([
            'appid' => 'mock-appid',
            'template_id' => 'mock-template-id',
            'url' => '',
            'miniprogram' => [
                'appid' => 'mock-appid',
            ],
            'data' => [],
        ], $client->formatMpMessage([
            'appid' => 'mock-appid',
            'template_id' => 'mock-template-id',
            'miniprogram' => [
                'appid' => '',
            ],
            'data' => [],
        ]));
    }

    public function testFormatWeappMessage()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        $this->assertSame([
            'template_id' => 'mock-template-id',
            'page' => 'mock-page',
            'form_id' => 'mock-form-id',
            'data' => [
                'foo' => ['value' => 'string'],
                'bar' => ['value' => 'content', 'color' => '#F00'],
                'baz' => ['value' => 'hello', 'color' => '#550038'],
                'zoo' => ['value' => 'hello'],
            ],
            'emphasis_keyword' => '',
        ], $client->formatWeappMessage([
            'template_id' => 'mock-template-id',
            'page' => 'mock-page',
            'form_id' => 'mock-form-id',
            'data' => [
                'foo' => 'string',
                'bar' => ['content', '#F00'],
                'baz' => ['value' => 'hello', 'color' => '#550038'],
                'zoo' => ['value' => 'hello'],
            ],
        ]));

        // miss template_id
        try {
            $client->formatWeappMessage([
                'template_id' => '',
                'page' => 'mock-page',
                'form_id' => 'mock-from-id',
                'data' => [],
            ]);
        } catch (\Exception $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertSame('Attribute "template_id" can not be empty!', $exception->getMessage());
        }

        // miss from_id
        try {
            $client->formatWeappMessage([
                'template_id' => 'mock-template-id',
                'page' => 'mock-page',
                'form_id' => '',
                'data' => [],
            ]);
        } catch (\Exception $exception) {
            $this->assertInstanceOf(InvalidArgumentException::class, $exception);
            $this->assertSame('Attribute "form_id" can not be empty!', $exception->getMessage());
        }
    }
}
