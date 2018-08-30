<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Material;

use EasyWeChat\Kernel\Http\Response;
use EasyWeChat\Kernel\Http\StreamResponse;
use EasyWeChat\Kernel\Messages\Article;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OfficialAccount\Material\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testUploadImage()
    {
        $client = $this->mockApiClient(Client::class, ['upload']);

        $client->expects()->upload('image', '/path/to/media')->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->uploadImage('/path/to/media'));
    }

    public function testUploadVoice()
    {
        $client = $this->mockApiClient(Client::class, ['upload']);

        $client->expects()->upload('voice', '/path/to/media')->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->uploadVoice('/path/to/media'));
    }

    public function testUploadThumb()
    {
        $client = $this->mockApiClient(Client::class, ['upload']);

        $client->expects()->upload('thumb', '/path/to/media')->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->uploadThumb('/path/to/media'));
    }

    public function testUploadVideo()
    {
        $client = $this->mockApiClient(Client::class, ['upload']);

        $client->expects()->upload('video', '/path/to/media', [
            'description' => json_encode([
               'title' => 'mock-title',
                'introduction' => 'mock-introduction',
            ]),
        ])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->uploadVideo('/path/to/media', 'mock-title', 'mock-introduction'));
    }

    public function testUploadArticle()
    {
        $client = $this->mockApiClient(Client::class);

        // Article instance
        $article1 = new Article([
            'thumb_media_id' => 'mock-thm-id',
            'author' => 'overtrue',
            'title' => 'easywechat 3.0',
            'content' => 'easywechat 3.0 ...',
            'digest' => 'easywechat 3 介绍',
            'source_url' => 'http://www.easywechat.com/path/to/source',
            'show_cover' => true,
        ]);

        $article2 = new Article([
            'thumb_media_id' => 'mock-thm-id',
            'author' => 'overtrue',
            'title' => 'easywechat 4.0',
            'content' => 'easywechat 4.0 ...',
            'digest' => 'easywechat 4 介绍',
            'source_url' => 'http://www.easywechat.com/path/to/source',
            'show_cover' => true,
        ]);

        // case1: ['title', ...]
        $client->expects()->httpPostJson('cgi-bin/material/add_news', [
            'articles' => [
                $article1->all(),
            ],
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->uploadArticle($article1->all()));

        // case2: Article
        $client->expects()->httpPostJson('cgi-bin/material/add_news', [
            'articles' => [
                $article1->transformForJsonRequestWithoutType([]),
            ],
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->uploadArticle($article1));

        // case3: [Article, Article]
        $client->expects()->httpPostJson('cgi-bin/material/add_news', [
            'articles' => [
                $article1->transformForJsonRequestWithoutType(),
                $article2->transformForJsonRequestWithoutType(),
            ],
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->uploadArticle([$article1, $article2]));
    }

    public function testUpdateArticle()
    {
        $client = $this->mockApiClient(Client::class);

        // Article instance
        $article = new Article([
            'thumb_media_id' => 'mock-thm-id',
            'author' => 'overtrue',
            'title' => 'easywechat 3.0',
            'content' => 'easywechat 3.0 ...',
            'digest' => 'easywechat 3 介绍',
            'source_url' => 'http://www.easywechat.com/path/to/source',
            'show_cover' => true,
        ]);
        // case1: Article
        $client->expects()->httpPostJson('cgi-bin/material/update_news', [
            'media_id' => 'mock-media-id',
            'index' => 3,
            'articles' => $article->transformForJsonRequestWithoutType(),
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->updateArticle('mock-media-id', $article, 3));

        // case2: Article array
        $client->expects()->httpPostJson('cgi-bin/material/update_news', [
            'media_id' => 'mock-media-id',
            'index' => 3,
            'articles' => $article->all(),
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->updateArticle('mock-media-id', $article->all(), 3));
    }

    public function testUploadArticleImage()
    {
        $client = $this->mockApiClient(Client::class, ['upload']);

        $client->expects()->upload('news_image', '/path/to/media')->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->uploadArticleImage('/path/to/media'));
    }

    public function testGet()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['response_type' => 'array']));

        // stream response
        $response = new Response(200, ['Content-Disposition' => 'attachment; filename="filename.jpg"'], 'mock-content');
        $client->expects()->requestRaw('cgi-bin/material/get_material', 'POST', ['json' => ['media_id' => 'mock-media-id']])
                    ->andReturn($response)->once();

        $this->assertInstanceOf(StreamResponse::class, $client->get('mock-media-id'));

        // json response
        $response = new Response(200, ['Content-Type' => ['text/plain']], '{"title": "mock-title"}');
        $client->expects()->requestRaw('cgi-bin/material/get_material', 'POST', ['json' => ['media_id' => 'mock-media-id']])
                    ->andReturn($response)->once();

        $result = $client->get('mock-media-id');
        $this->assertInternalType('array', $result);
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/material/del_material', ['media_id' => 'mock-media-id'])
                ->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->delete('mock-media-id'));
    }

    public function testList()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/material/batchget_material', [
            'type' => 'image',
            'offset' => 0,
            'count' => 20,
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->list('image'));

        $client->expects()->httpPostJson('cgi-bin/material/batchget_material', [
            'type' => 'image',
            'offset' => 1,
            'count' => 20,
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->list('image', 1));

        $client->expects()->httpPostJson('cgi-bin/material/batchget_material', [
            'type' => 'image',
            'offset' => 1,
            'count' => 10,
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->list('image', 1, 10));
    }

    public function testStats()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpGet('cgi-bin/material/get_materialcount')
                            ->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->stats());
    }

    public function testupload()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        // invalid path
        $path = '/this/is/a/not/exists/image.jpg';

        try {
            $client->upload('image', $path);
            $this->fail('Failed to assert exception thrown.');
        } catch (\Exception $e) {
            $this->assertSame(sprintf('File does not exist, or the file is unreadable: "%s"', $path), $e->getMessage());
        }

        // real path
        $path = STUBS_ROOT.'/files/image.jpg';
        $client->expects()->httpUpload('cgi-bin/material/add_material', ['media' => $path], ['foo' => 'bar', 'type' => 'image'])
                    ->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->upload('image', $path, ['foo' => 'bar']));

        // real path with news image
        $client->expects()->httpUpload('cgi-bin/media/uploadimg', ['media' => $path], ['foo' => 'bar', 'type' => 'news_image'])
            ->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->upload('news_image', $path, ['foo' => 'bar']));
    }
}
