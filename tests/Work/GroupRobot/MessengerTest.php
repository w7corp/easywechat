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

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\GroupRobot\Client;
use EasyWeChat\Work\GroupRobot\Messages\Image;
use EasyWeChat\Work\GroupRobot\Messages\Markdown;
use EasyWeChat\Work\GroupRobot\Messages\News;
use EasyWeChat\Work\GroupRobot\Messages\NewsItem;
use EasyWeChat\Work\GroupRobot\Messages\Text;
use EasyWeChat\Work\GroupRobot\Messenger;

class MessengerTest extends TestCase
{
    public function testMessage()
    {
        $client = \Mockery::mock(Client::class);
        $messenger = new Messenger($client);

        $this->assertInstanceOf(Text::class, $messenger->message('hello')->message);
        $this->assertInstanceOf(Text::class, $messenger->message(12345)->message);

        try {
            $messenger->foo;
        } catch (\Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame('No property named "foo"', $e->getMessage());
        }

        try {
            $messenger->message(false);
        } catch (\Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame('Invalid message.', $e->getMessage());
        }

        try {
            $messenger->message([]);
        } catch (\Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame('Invalid message.', $e->getMessage());
        }

        try {
            $messenger->message(new \stdClass());
        } catch (\Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame('Invalid message.', $e->getMessage());
        }
    }

    public function testGroupKey()
    {
        $client = \Mockery::mock(Client::class);
        $messenger = new Messenger($client);

        $this->assertSame('mock-key', $messenger->toGroup('mock-key')->groupKey);
    }

    public function testSendWithoutContent()
    {
        $client = \Mockery::mock(Client::class);
        $messenger = new Messenger($client);

        try {
            $messenger->send();
            $this->fail('No expected exception thrown.');
        } catch (\Exception $e) {
            $this->assertInstanceOf(RuntimeException::class, $e);
            $this->assertSame('No message to send.', $e->getMessage());
        }
    }

    public function testSendWithoutGroupKey()
    {
        $client = \Mockery::mock(Client::class);
        $messenger = new Messenger($client);

        try {
            $messenger->send('hello');
            $this->fail('No expected exception thrown.');
        } catch (\Exception $e) {
            $this->assertInstanceOf(RuntimeException::class, $e);
            $this->assertSame('No group key specified.', $e->getMessage());
        }
    }

    public function testSendWithText()
    {
        $client = \Mockery::mock(Client::class);
        $messenger = new Messenger($client);

        $client->expects()->send('mock-key', [
            'msgtype' => 'text',
            'text' => [
                'content' => 'hello',
                'mentioned_list' => [],
                'mentioned_mobile_list' => [],
            ],
        ])->times(2)->andReturn('mock-result');

        $message = new Text('hello');

        $this->assertSame('mock-result', $messenger->toGroup('mock-key')->send('hello'));
        $this->assertSame('mock-result', $messenger->message($message)->toGroup('mock-key')->send());
    }

    public function testSendWithMention()
    {
        $client = \Mockery::mock(Client::class);
        $messenger = new Messenger($client);

        $client->expects()->send('mock-key', [
            'msgtype' => 'text',
            'text' => [
                'content' => 'hello',
                'mentioned_list' => ['mock-userid', '@all'],
                'mentioned_mobile_list' => ['mock-mobile', 'mock-mobile2'],
            ],
        ])->times(2)->andReturn('mock-result');

        $userIds = ['mock-userid', '@all'];
        $mobiles = ['mock-mobile', 'mock-mobile2'];

        $message = new Text('hello', $userIds, $mobiles);

        $this->assertSame('mock-result', $messenger->message($message)->toGroup('mock-key')->send());

        $message = new Text('hello');
        $message->mention($userIds)->mentionByMobile($mobiles);

        $this->assertSame('mock-result', $messenger->message($message)->toGroup('mock-key')->send());
    }

    public function testSendWithMarkdown()
    {
        $client = \Mockery::mock(Client::class);
        $messenger = new Messenger($client);

        $client->expects()->send('mock-key', [
            'msgtype' => 'markdown',
            'markdown' => [
                'content' => 'hello',
            ],
        ])->andReturn('mock-result');

        $message = new Markdown('hello');

        $this->assertSame('mock-result', $messenger->message($message)->toGroup('mock-key')->send());
    }

    public function testSendWithImage()
    {
        $client = \Mockery::mock(Client::class);
        $messenger = new Messenger($client);

        $client->expects()->send('mock-key', [
            'msgtype' => 'image',
            'image' => [
                'base64' => 'mock-base64',
                'md5' => 'mock-md5',
            ],
        ])->andReturn('mock-result');

        $message = new Image('mock-base64', 'mock-md5');

        $this->assertSame('mock-result', $messenger->message($message)->toGroup('mock-key')->send());
    }

    public function testSendWithNews()
    {
        $client = \Mockery::mock(Client::class);
        $messenger = new Messenger($client);

        $client->expects()->send('mock-key', [
            'msgtype' => 'news',
            'news' => [
                'articles' => [
                    [
                        'title' => 'mock-title',
                        'description' => 'mock-description',
                        'url' => 'mock-url',
                        'picurl' => 'mock-picurl',
                    ],
                ],
            ],
        ])->andReturn('mock-result');

        $message = new News([
            new NewsItem([
                'title' => 'mock-title',
                'description' => 'mock-description',
                'url' => 'mock-url',
                'image' => 'mock-picurl',
            ]),
        ]);

        $this->assertSame('mock-result', $messenger->message($message)->toGroup('mock-key')->send());
    }
}
