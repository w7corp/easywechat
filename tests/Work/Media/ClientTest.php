<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\Media;

use EasyWeChat\Kernel\Http\Response;
use EasyWeChat\Kernel\Http\StreamResponse;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Media\Client;

class ClientTest extends TestCase
{
    public function testGet()
    {
        $app = new ServiceContainer();
        $client = $this->mockApiClient(Client::class, [], $app);

        $mediaId = 'invalid-media-id';
        $imageResponse = new Response(200, ['content-type' => 'text/plain'], '{"error": "invalid media id hits."}');
        $client->expects()->requestRaw('cgi-bin/media/get', 'GET', [
            'query' => [
                'media_id' => $mediaId,
            ],
        ])->andReturn($imageResponse)->once();

        $this->assertSame(['error' => 'invalid media id hits.'], $client->get($mediaId));

        $mediaId = 'valid-media-id';
        $imageResponse = new Response(200, [], 'valid data');
        $client->expects()->requestRaw('cgi-bin/media/get', 'GET', [
            'query' => [
                'media_id' => $mediaId,
            ],
        ])->andReturn($imageResponse)->once();

        $this->assertInstanceOf(StreamResponse::class, $client->get($mediaId));
    }

    public function testUploadImage()
    {
        $client = $this->mockApiClient(Client::class, ['upload']);
        $client->expects()->upload('image', '/foo/bar/image.jpg')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->uploadImage('/foo/bar/image.jpg'));
    }

    public function testUploadVideo()
    {
        $client = $this->mockApiClient(Client::class, ['upload']);
        $client->expects()->upload('video', '/foo/bar/video.mp4')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->uploadVideo('/foo/bar/video.mp4'));
    }

    public function testUploadVoice()
    {
        $client = $this->mockApiClient(Client::class, ['upload']);
        $client->expects()->upload('voice', '/foo/bar/voice.mp3')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->uploadVoice('/foo/bar/voice.mp3'));
    }

    public function testUploadFile()
    {
        $client = $this->mockApiClient(Client::class, ['upload']);
        $client->expects()->upload('file', '/foo/bar/file.txt')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->uploadFile('/foo/bar/file.txt'));
    }

    public function testUpload()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpUpload('cgi-bin/media/upload', [
            'media' => '/foo/bar/voice.mp3',
        ], [], ['type' => 'voice'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->upload('voice', '/foo/bar/voice.mp3'));
    }
}
