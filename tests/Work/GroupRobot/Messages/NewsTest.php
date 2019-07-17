<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Work\GroupRobot\Messages;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\GroupRobot\Messages\Message;
use EasyWeChat\Work\GroupRobot\Messages\News;
use EasyWeChat\Work\GroupRobot\Messages\NewsItem;

class NewsTest extends TestCase
{
    public function testBasicFeatures()
    {
        $items = [
            new NewsItem([
                'title' => 'mock-title',
                'description' => 'mock-description',
                'url' => 'mock-url',
                'image' => 'mock-picurl',
            ]),
        ];

        $news = new News($items);

        $message = [
            'msgtype' => 'news',
            'news' => [
                'articles' => [
                    [
                        'title' => 'mock-title',
                        'description' => 'mock-description',
                        'url' => 'mock-url',
                        'picurl' => 'mock-picurl',
                    ],
                ],
            ],
        ];

        $this->assertSame($message, $news->transformForJsonRequest());
        $this->assertInstanceOf(Message::class, $news);
    }
}
