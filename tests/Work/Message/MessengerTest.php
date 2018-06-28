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

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Messages\Raw;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Message\Client;
use EasyWeChat\Work\Message\Messenger;

class MessengerTest extends TestCase
{
    public function testMessage()
    {
        $client = \Mockery::mock(Client::class);
        $messenger = new Messenger($client);

        $this->assertInstanceOf(Text::class, $messenger->message('hello world!')->message);
        $this->assertInstanceOf(Text::class, $messenger->message(12345)->message);

        // invalid property
        try {
            $messenger->foo;
        } catch (\Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame('No property named "foo"', $e->getMessage());
        }

        // invalid
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

    public function testAgendId()
    {
        $client = \Mockery::mock(Client::class);
        $messenger = new Messenger($client);

        $this->assertSame(12345, $messenger->ofAgent(12345)->agentId);
    }

    public function testSetRecipients()
    {
        $client = \Mockery::mock(Client::class);
        $messenger = new Messenger($client);

        // default
        $this->assertSame(['touser' => '@all'], $messenger->to);

        // to user
        $this->assertSame(['touser' => 'overtrue'], $messenger->toUser('overtrue')->to);
        $this->assertSame(['touser' => 'overtrue|iovertrue'], $messenger->toUser(['overtrue', 'iovertrue'])->to);

        // to party
        $this->assertSame(['toparty' => 'party1'], $messenger->toParty('party1')->to);
        $this->assertSame(['toparty' => 'party1|party2'], $messenger->toParty(['party1', 'party2'])->to);

        // to tag
        $this->assertSame(['totag' => 'tag1'], $messenger->toTag('tag1')->to);
        $this->assertSame(['totag' => 'tag1|tag2'], $messenger->toTag(['tag1', 'tag2'])->to);
    }

    public function testSecretive()
    {
        $client = \Mockery::mock(Client::class);
        $messenger = new Messenger($client);

        $this->assertFalse($messenger->secretive);
        $this->assertTrue($messenger->secretive()->secretive);

        $message = new Raw(json_encode([
            'touser' => '@all',
            'msgtype' => 'text',
            'agentid' => 123456,
            'safe' => 0,
            'text' => [
                'content' => 'hello world!',
            ],
        ]));
        $client->expects()->send([
            'touser' => '@all',
            'msgtype' => 'text',
            'agentid' => 123456,
            'safe' => 0,
            'text' => [
                'content' => 'hello world!',
            ],
        ])->andReturn('mock-result');

        $messenger->message($message)->ofAgent(123456)->send();
        $this->assertFalse($messenger->secretive);
    }

    public function testSend()
    {
        $client = \Mockery::mock(Client::class);
        $messenger = new Messenger($client);

        // no message
        try {
            $messenger->send();
            $this->fail('No expected exception thrown.');
        } catch (\Exception $e) {
            $this->assertSame('No message to send.', $e->getMessage());
        }

        // no agentid
        $client = \Mockery::mock(Client::class);
        $messenger = new Messenger($client);

        try {
            $messenger->send('hello world!');
            $this->fail('No expected exception thrown.');
        } catch (\Exception $e) {
            $this->assertSame('No agentid specified.', $e->getMessage());
        }

        // raw message
        $client = \Mockery::mock(Client::class);
        $messenger = new Messenger($client);
        $message = new Raw(json_encode([
            'touser' => '@all',
            'msgtype' => 'text',
            'agentid' => 123456,
            'safe' => 0,
            'text' => [
                'content' => 'hello world!',
            ],
        ]));
        $client->expects()->send([
            'touser' => '@all',
            'msgtype' => 'text',
            'agentid' => 123456,
            'safe' => 0,
            'text' => [
                'content' => 'hello world!',
            ],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $messenger->message($message)->ofAgent(123456)->send());

        // not raw message
        $client = \Mockery::mock(Client::class);
        $messenger = new Messenger($client);
        $client->expects()->send([
            'touser' => '@all',
            'msgtype' => 'text',
            'agentid' => 123456,
            'safe' => 0,
            'text' => [
                'content' => 'hello world!',
            ],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $messenger->ofAgent(123456)->send('hello world!'));
    }
}
