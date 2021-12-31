<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\FreePublish;

use EasyWeChat\OfficialAccount\FreePublish\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testGet()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/freepublish/get', [
            'publish_id' => 'mock-publish-id'
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->get('mock-publish-id'));
    }

    public function testSubmit()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/freepublish/submit', ['media_id' => 'mock-media-id'])
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->submit('mock-media-id'));
    }

    public function testGetArticle()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/freepublish/getarticle', ['article_id' => 'mock-article-id'])
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getArticle('mock-article-id'));
    }

    public function testBatchGet()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/freepublish/batchget', [
            'offset' => 0,
            'count' => 20,
            'no_content' => 0,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->batchGet());

        $client->expects()->httpPostJson('cgi-bin/freepublish/batchget', [
            'offset' => 1,
            'count' => 20,
            'no_content' => 0,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->batchGet(1));

        $client->expects()->httpPostJson('cgi-bin/freepublish/batchget', [
            'offset' => 10,
            'count' => 10,
            'no_content' => 1,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->batchGet(10, 10, 1));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/freepublish/delete', ['article_id' => 'mock-article-id'])
                ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->delete('mock-article-id'));
    }
}
