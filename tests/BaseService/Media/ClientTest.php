<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\BaseService\Media;

use EasyWeChat\BaseService\Media\Client;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Http\Response;
use EasyWeChat\Kernel\Http\StreamResponse;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\Tests\TestCase;
use Monolog\Logger;

class ClientTest extends TestCase
{
    public function testUploadImage()
    {
        $client = $this->mockApiClient(Client::class, ['upload']);
        $client->expects()->upload('image', '/foo/bar/image.jpg')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->uploadImage('/foo/bar/image.jpg'));
    }

    public function testVideoImage()
    {
        $client = $this->mockApiClient(Client::class, ['upload']);
        $client->expects()->upload('video', '/foo/bar/video.mp4')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->uploadVideo('/foo/bar/video.mp4'));
    }

    public function testVoiceImage()
    {
        $client = $this->mockApiClient(Client::class, ['upload']);
        $client->expects()->upload('voice', '/foo/bar/voice.mp3')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->uploadVoice('/foo/bar/voice.mp3'));
    }

    public function testThumbImage()
    {
        $client = $this->mockApiClient(Client::class, ['upload']);
        $client->expects()->upload('thumb', '/foo/bar/thumb.jpg')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->uploadThumb('/foo/bar/thumb.jpg'));
    }

    public function testUpload()
    {
        $client = $this->mockApiClient(Client::class, ['httpUpload']);

        $path = STUBS_ROOT.'/files/image.jpg';
        $client->expects()->httpUpload('media/upload', ['media' => $path], ['type' => 'image'])->andReturn('mock-response')->once();

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

    public function testDownload()
    {
        $client = $this->mockApiClient(Client::class, ['getStream']);

        try {
            $client->download('media-id', '/not-exists-directory');
            $this->fail('No expected exception thrown.');
        } catch (\Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame('Directory does not exist or is not writable: \'/not-exists-directory\'.', $e->getMessage());
        }

        $directory = '/tmp/';
        $response = \Mockery::mock(StreamResponse::class);

        // no filename
        $response->expects()->saveAs($directory, 'media-id')->once();
        $client->expects()->getStream('media-id')->andReturn($response)->twice();

        $this->assertSame('media-id', $client->download('media-id', $directory));

        // with filename
        $response->expects()->saveAs($directory, 'custom-filename')->once();
        $this->assertSame('custom-filename', $client->download('media-id', $directory, 'custom-filename'));
    }

    public function testGetStream()
    {
        $app = new ServiceContainer();
        $logger = \Mockery::mock(Logger::class);
        $logger->expects()->error('Fail to get media contents.', [
            'error' => 'invalid media id hits.',
        ]);
        $app['logger'] = $logger;
        $client = $this->mockApiClient(Client::class, [], $app);

        $mediaId = 'media-id';
        $imageResponse = new Response(200, ['content-type' => 'text/plain'], '{"error": "invalid media id hits."}');
        $client->expects()->requestRaw('media/get', 'GET', [
            'query' => [
                'media_id' => $mediaId,
            ],
        ])->andReturn($imageResponse)->once();

        $response = $client->getStream($mediaId);
        $this->assertInstanceOf(StreamResponse::class, $response);
        $this->assertSame('{"error": "invalid media id hits."}', $response->getBody()->getContents());
    }
}
