<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\BasicService\Media;

use EasyWeChat\BasicService\Media\Client;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Http\Response;
use EasyWeChat\Kernel\Http\StreamResponse;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
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

    public function testUploadThumb()
    {
        $client = $this->mockApiClient(Client::class, ['upload']);
        $client->expects()->upload('thumb', '/foo/bar/thumb.jpg')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->uploadThumb('/foo/bar/thumb.jpg'));
    }

    public function testUpload()
    {
        $client = $this->mockApiClient(Client::class, ['httpUpload']);

        $path = STUBS_ROOT.'/files/image.jpg';
        $client->expects()->httpUpload('media/upload', ['media' => $path], ['type' => 'image'])->andReturn('mock-response');

        $client->upload('image', $path);

        try {
            $client->upload('image', '/the-not-exists-path/invalid.jpg');
            $this->fail('No expected exception thrown.');
        } catch (\Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame('File does not exist, or the file is unreadable: \'/the-not-exists-path/invalid.jpg\'', $e->getMessage());
        }

        try {
            $client->upload('mp4', $path);
            $this->fail('No expected exception thrown.');
        } catch (\Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame('Unsupported media type: \'mp4\'', $e->getMessage());
        }
    }

    public function testUploadVideoForBroadcasting()
    {
        $path = '/path/to/video.mp4';
        $client = $this->mockApiClient(Client::class, ['uploadVideo', 'detectAndCastResponseToType', 'createVideoForBroadcasting']);
        $client->allows()->uploadVideo($path)->andReturn('mock-response');

        // no media_id
        $client->expects()->detectAndCastResponseToType('mock-response', 'array')->andReturn(['foo' => 'bar']);
        $client->shouldNotReceive('createVideoForBroadcasting');

        $client->uploadVideoForBroadcasting($path, 'title', 'description');

        // return media_id
        $client->expects()->detectAndCastResponseToType('mock-response', 'array')->andReturn(['media_id' => 'mock-media-id']);
        $client->expects()->createVideoForBroadcasting('mock-media-id', 'title', 'description');

        $client->uploadVideoForBroadcasting($path, 'title', 'description');
    }

    public function testCreateVideoForBroadcasting()
    {
        $client = $this->mockApiClient(Client::class);
        $mediaId = 'mock-media-id';
        $title = 'mock-title';
        $description = 'mock-description';
        $client->expects()->httpPostJson('media/uploadvideo', [
            'media_id' => $mediaId,
            'title' => $title,
            'description' => $description,
        ])->andReturn('mock-response');

        $this->assertSame('mock-response', $client->createVideoForBroadcasting($mediaId, $title, $description));
    }

    public function testGet()
    {
        $app = new ServiceContainer();
        $client = $this->mockApiClient(Client::class, [], $app);

        $mediaId = 'invalid-media-id';
        $imageResponse = new Response(200, ['content-type' => 'text/plain'], '{"error": "invalid media id hits."}');
        $client->expects()->requestRaw('media/get', 'GET', [
            'query' => [
                'media_id' => $mediaId,
            ],
        ])->andReturn($imageResponse);

        $this->assertSame(['error' => 'invalid media id hits.'], $client->get($mediaId));

        $mediaId = 'valid-media-id';
        $imageResponse = new Response(200, ['content-disposition' => 'attachment'], 'valid data');
        $client->expects()->requestRaw('media/get', 'GET', [
            'query' => [
                'media_id' => $mediaId,
            ],
        ])->andReturn($imageResponse);

        $this->assertInstanceOf(StreamResponse::class, $client->get($mediaId));
    }

    public function testGetJssdkMedia()
    {
        $app = new ServiceContainer();
        $client = $this->mockApiClient(Client::class, [], $app);

        $mediaId = 'invalid-media-id';
        $imageResponse = new Response(200, ['content-type' => 'text/plain'], '{"error": "invalid media id hits."}');
        $client->expects()->requestRaw('media/get/jssdk', 'GET', [
            'query' => [
                'media_id' => $mediaId,
            ],
        ])->andReturn($imageResponse);

        $this->assertSame(['error' => 'invalid media id hits.'], $client->getJssdkMedia($mediaId));

        $mediaId = 'valid-media-id';
        $imageResponse = new Response(200, ['content-disposition' => 'attachment'], 'valid data');
        $client->expects()->requestRaw('media/get/jssdk', 'GET', [
            'query' => [
                'media_id' => $mediaId,
            ],
        ])->andReturn($imageResponse);

        $this->assertInstanceOf(StreamResponse::class, $client->getJssdkMedia($mediaId));
    }
}
