<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Draft;

use EasyWeChat\OfficialAccount\Draft\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testAdd()
    {
        $client = $this->mockApiClient(Client::class);

        // articles
        $article1 = [
            'title' => 'easywechat 3.0',
            'author' => 'overtrue',
            'content' => 'easywechat 3.0 ...',
            'digest' => 'easywechat 3 介绍',
            'content_source_url' => 'http://www.easywechat.com/path/to/source',
            'thumb_media_id' => 'mock-media-id',
            'show_cover_pic' => 1,
            'need_open_comment' => 0,
            'only_fans_can_comment' => 0,
        ];

        $article2 = [
            'title' => 'easywechat 4.0',
            'author' => 'overtrue',
            'content' => 'easywechat 4.0 ...',
            'digest' => 'easywechat 4 介绍',
            'content_source_url' => 'http://www.easywechat.com/path/to/source',
            'thumb_media_id' => 'mock-media-id',
            'show_cover_pic' => 1,
            'need_open_comment' => 0,
            'only_fans_can_comment' => 0,
        ];

        $client->expects()->httpPostJson('cgi-bin/draft/add', [
            'articles' => [
                $article1,
                $article2,
            ],
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->add(['articles' => [$article1, $article2]]));
    }

    public function testGet()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/draft/get', [
            'media_id' => 'mock-media-id'
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->get('mock-media-id'));
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(Client::class);

        // article
        $article = [
            'title' => 'easywechat 4.0',
            'author' => 'overtrue',
            'content' => 'easywechat 4.0 ...',
            'digest' => 'easywechat 4 介绍',
            'content_source_url' => 'http://www.easywechat.com/path/to/source',
            'thumb_media_id' => 'mock-media-id',
            'show_cover_pic' => 1,
            'need_open_comment' => 0,
            'only_fans_can_comment' => 1,
        ];

        $client->expects()->httpPostJson('cgi-bin/draft/update', [
            'media_id' => 'mock-media-id',
            'index' => 3,
            'articles' => $article,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->update('mock-media-id', 3, $article));
    }

    public function testCount()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/draft/count')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->count());
    }

    public function testBatchGet()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/draft/batchget', [
            'offset' => 0,
            'count' => 20,
            'no_content' => 0,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->batchGet());

        $client->expects()->httpPostJson('cgi-bin/draft/batchget', [
            'offset' => 1,
            'count' => 20,
            'no_content' => 0,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->batchGet(1));

        $client->expects()->httpPostJson('cgi-bin/draft/batchget', [
            'offset' => 10,
            'count' => 10,
            'no_content' => 1,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->batchGet(10, 10, 1));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/draft/delete', ['media_id' => 'mock-media-id'])
                ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->delete('mock-media-id'));
    }
}
