<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Broadcasting;

use EasyWeChat\Kernel\Messages\Card;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\Media;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\OfficialAccount\Broadcasting\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testSend()
    {
        $c = $this->mockApiClient(Client::class);

        // to all
        $message = [
            'filter' => [
                'is_to_all' => true,
            ],
            'data' => [
                'foo' => 'bar',
            ],
        ];
        $c->expects()->httpPostJson('cgi-bin/message/mass/sendall', $message)->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $c->send($message));

        // to group
        $message = [
            'filter' => [
                'is_to_all' => false,
                'group' => '@xxx',
            ],
            'data' => [
                'foo' => 'bar',
            ],
        ];
        $c->expects()->httpPostJson('cgi-bin/message/mass/send', $message)->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $c->send($message));
    }

    public function testPreview()
    {
        $c = $this->mockApiClient(Client::class);

        $message = [
            'data' => [
                'foo' => 'bar',
            ],
        ];
        $c->expects()->httpPostJson('cgi-bin/message/mass/preview', $message)->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $c->preview($message));
    }

    public function testDelete()
    {
        $c = $this->mockApiClient(Client::class);
        $msgId = 'mock-msg-id';
        $c->expects()->httpPostJson('cgi-bin/message/mass/delete', [
            'msg_id' => $msgId,
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $c->delete($msgId));
    }

    public function testStatus()
    {
        $c = $this->mockApiClient(Client::class);
        $msgId = 'mock-msg-id';
        $c->expects()->httpPostJson('cgi-bin/message/mass/get', [
            'msg_id' => $msgId,
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $c->status($msgId));
    }

    public function testSendText()
    {
        $c = $this->mockApiClient(Client::class, ['sendMessage']);
        $c->expects()->sendMessage(\Mockery::on(function ($message) {
            return $message instanceof Text && $message->content === 'hello world!';
        }), 'overtrue')->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $c->sendText('hello world!', 'overtrue'));
    }

    public function testSendNews()
    {
        $c = $this->mockApiClient(Client::class, ['sendMessage']);
        $c->expects()->sendMessage(\Mockery::on(function ($message) {
            return $message instanceof Media && $message->getType() === 'mpnews' && $message->media_id === 'mock-media-id';
        }), 'overtrue')->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $c->sendNews('mock-media-id', 'overtrue'));
    }

    public function testSendVoice()
    {
        $c = $this->mockApiClient(Client::class, ['sendMessage']);
        $c->expects()->sendMessage(\Mockery::on(function ($message) {
            return $message instanceof Media && $message->getType() === 'voice' && $message->media_id === 'mock-media-id';
        }), 'overtrue')->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $c->sendVoice('mock-media-id', 'overtrue'));
    }

    public function testSendVideo()
    {
        $c = $this->mockApiClient(Client::class, ['sendMessage']);
        $c->expects()->sendMessage(\Mockery::on(function ($message) {
            return $message instanceof Media && $message->getType() === 'mpvideo' && $message->media_id === 'mock-media-id';
        }), 'overtrue')->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $c->sendVideo('mock-media-id', 'overtrue'));
    }

    public function testSendImage()
    {
        $c = $this->mockApiClient(Client::class, ['sendMessage']);
        $c->expects()->sendMessage(\Mockery::on(function ($message) {
            return $message instanceof Image && $message->media_id === 'mock-media-id';
        }), 'overtrue')->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $c->sendImage('mock-media-id', 'overtrue'));
    }

    public function testSendCard()
    {
        $c = $this->mockApiClient(Client::class, ['sendMessage']);
        $c->expects()->sendMessage(\Mockery::on(function ($message) {
            return $message instanceof Card && $message->card_id === 'mock-card-id';
        }), 'overtrue')->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $c->sendCard('mock-card-id', 'overtrue'));
    }

    public function testPreviewText()
    {
        $c = $this->mockApiClient(Client::class, ['previewMessage']);
        $c->expects()->previewMessage(\Mockery::on(function ($message) {
            return $message instanceof Text && $message->content === 'hello world!';
        }), 'openid', Client::PREVIEW_BY_OPENID)->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $c->previewText('hello world!', 'openid'));
    }

    public function testPreviewNews()
    {
        $c = $this->mockApiClient(Client::class, ['previewMessage']);
        $c->expects()->previewMessage(\Mockery::on(function ($message) {
            return $message instanceof Media && $message->getType() === 'mpnews' && $message->media_id === 'mock-media-id';
        }), 'openid', Client::PREVIEW_BY_OPENID)->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $c->previewNews('mock-media-id', 'openid'));
    }

    public function testPreviewVoice()
    {
        $c = $this->mockApiClient(Client::class, ['previewMessage']);
        $c->expects()->previewMessage(\Mockery::on(function ($message) {
            return $message instanceof Media && $message->getType() === 'voice' && $message->media_id === 'mock-media-id';
        }), 'openid', Client::PREVIEW_BY_OPENID)->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $c->previewVoice('mock-media-id', 'openid'));
    }

    public function testPreviewVideo()
    {
        $c = $this->mockApiClient(Client::class, ['previewMessage']);
        $c->expects()->previewMessage(\Mockery::on(function ($message) {
            return $message instanceof Media && $message->getType() === 'mpvideo' && $message->media_id === 'mock-media-id';
        }), 'openid', Client::PREVIEW_BY_OPENID)->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $c->previewVideo('mock-media-id', 'openid'));
    }

    public function testPreviewImage()
    {
        $c = $this->mockApiClient(Client::class, ['previewMessage']);
        $c->expects()->previewMessage(\Mockery::on(function ($message) {
            return $message instanceof Image && $message->media_id === 'mock-media-id';
        }), 'openid', Client::PREVIEW_BY_OPENID)->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $c->previewImage('mock-media-id', 'openid'));
    }

    public function testPreviewCard()
    {
        $c = $this->mockApiClient(Client::class, ['previewMessage']);
        $c->expects()->previewMessage(\Mockery::on(function ($message) {
            return $message instanceof Card && $message->card_id === 'mock-card-id';
        }), 'openid', Client::PREVIEW_BY_OPENID)->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $c->previewCard('mock-card-id', 'openid'));
    }

    public function testpreviewMessage()
    {
        $c = $this->mockApiClient(Client::class, ['preview']);

        // to openid
        $c->expects()->preview([
            'touser' => 'mock-openid',
            'msgtype' => 'text',
            'text' => [
                'content' => 'hello world!',
            ],
        ])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $c->previewMessage(new Text('hello world!'), 'mock-openid'));

        // to name
        $c->expects()->preview([
            'towxname' => 'overtrue',
            'msgtype' => 'text',
            'text' => [
                'content' => 'hello world!',
            ],
        ])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $c->previewMessage(new Text('hello world!'), 'overtrue', Client::PREVIEW_BY_NAME));
    }

    public function testSendMessage()
    {
        $c = $this->mockApiClient(Client::class, ['send']);

        // to openid
        $c->expects()->send([
            'filter' => [
                'is_to_all' => false,
                'group_id' => 'mock-groupid',
            ],
            'msgtype' => 'text',
            'text' => [
                'content' => 'hello world!',
            ],
        ])->andReturn('mock-result')->once();

        $c->sendMessage(new Text('hello world!'), 'mock-groupid');
    }
}
