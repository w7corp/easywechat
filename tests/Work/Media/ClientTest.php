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
        ])->andReturn($imageResponse);

        $this->assertSame(['error' => 'invalid media id hits.'], $client->get($mediaId));

        $mediaId = 'valid-media-id';
        $imageResponse = new Response(200, [], 'valid data');
        $client->expects()->requestRaw('cgi-bin/media/get', 'GET', [
            'query' => [
                'media_id' => $mediaId,
            ],
        ])->andReturn($imageResponse);

        $this->assertInstanceOf(StreamResponse::class, $client->get($mediaId));
    }

    public function testUploadImage()
    {

        //无参
        $client = $this->mockApiClient(Client::class);
        $files = [
            'media' => '/foo/bar/image.jpg',
        ];
        $form = [];
        $type = 'image';

        $client->expects()->httpUpload('cgi-bin/media/upload', $files, $form, compact('type'))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->uploadImage('/foo/bar/image.jpg'));

        //有参
        $client = $this->mockApiClient(Client::class, ['upload']);
        $client->expects()->upload('image', '/foo/bar/image.jpg', ['filename' => 'image.jpg'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->uploadImage('/foo/bar/image.jpg', ['filename' => 'image.jpg']));
    }

    public function testUploadVideo()
    {
        //无参
        $client = $this->mockApiClient(Client::class);

        $files = [
            'media' => '/foo/bar/video.mp4',
        ];
        $form = [];
        $type = 'video';

        $client->expects()->httpUpload('cgi-bin/media/upload', $files, $form, compact('type'))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->uploadVideo('/foo/bar/video.mp4'));

        //有参
        $client = $this->mockApiClient(Client::class, ['upload']);
        $client->expects()->upload('video', '/foo/bar/video.mp4', ['filename' => 'video.mp4'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->uploadVideo('/foo/bar/video.mp4', ['filename' => 'video.mp4']));
    }

    public function testUploadVoice()
    {
        //无参
        $client = $this->mockApiClient(Client::class);

        $files = [
            'media' => '/foo/bar/voice.mp3',
        ];
        $form = [];
        $type = 'voice';

        $client->expects()->httpUpload('cgi-bin/media/upload', $files, $form, compact('type'))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->uploadVoice('/foo/bar/voice.mp3'));

        //有参
        $client = $this->mockApiClient(Client::class, ['upload']);
        $client->expects()->upload('voice', '/foo/bar/voice.mp3', ['filename' => 'voice.mp3'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->uploadVoice('/foo/bar/voice.mp3', ['filename' => 'voice.mp3']));
    }

    public function testUploadFile()
    {
        //无参
        $client = $this->mockApiClient(Client::class);

        $files = [
            'media' => '/foo/bar/file.txt',
        ];
        $form = [];
        $type = 'file';

        $client->expects()->httpUpload('cgi-bin/media/upload', $files, $form, compact('type'))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->uploadFile('/foo/bar/file.txt'));

        //有参
        $client = $this->mockApiClient(Client::class, ['upload']);
        $client->expects()->upload('file', '/foo/bar/file.txt', ['filename' => 'file.jpg'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->uploadFile('/foo/bar/file.txt', ['filename' => 'file.jpg']));
    }

    public function testUpload()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpUpload('cgi-bin/media/upload', [
            'media' => '/foo/bar/voice.mp3',
        ], [], ['type' => 'voice'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->upload('voice', '/foo/bar/voice.mp3'));

        //有参
        $client->expects()->httpUpload('cgi-bin/media/upload', [
            'media' => '/foo/bar/voice.mp3',
        ], ['filename' => 'voice.mp3'], ['type' => 'voice'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->upload('voice', '/foo/bar/voice.mp3', ['filename' => 'voice.mp3']));
    }
}
