<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MiniProgram\Search;

use EasyWeChat\MiniProgram\Search\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testSearch()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        $pages = [
            [
                'path' => 'pages/index/index',
                'query' => 'userName=wechat_user',
            ],
            [
                'path' => 'pages/articles/index',
                'query' => 'article_id=123456',
            ],
        ];

        $client->expects()->httpPostJson('wxa/search/wxaapi_submitpages', compact('pages'))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->submitPage($pages));
    }
}
