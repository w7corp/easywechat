<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Kernel\Messages;

use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Tests\TestCase;

class NewsTest extends TestCase
{
    public function testTransformToJsonRequest()
    {
        $group = new News([
            new NewsItem([
                'title' => 'EasyWeChat 4.0 发布了',
                'description' => 'EasyWeChat 4.0 于今天发布了',
                'url' => 'http://easywechat.com/blog/4.0-released.html',
                'image' => 'http://img01.easywechat.com/4.0.jpg',
            ]),
            new NewsItem([
                'title' => 'EasyWeChat 4.0 入门指南',
                'description' => 'EasyWeChat 4.0 于今天发布了，来看看新版用法',
                'url' => 'http://easywechat.com/blog/4.0-tutorial.html',
                'image' => 'http://img01.easywechat.com/4.0-tutorial.jpg',
            ]),
        ]);

        $this->assertSame([
            'msgtype' => 'news',
            'news' => [
                'articles' => [
                    [
                        'title' => 'EasyWeChat 4.0 发布了',
                        'description' => 'EasyWeChat 4.0 于今天发布了',
                        'url' => 'http://easywechat.com/blog/4.0-released.html',
                        'picurl' => 'http://img01.easywechat.com/4.0.jpg',
                    ],
                    [
                        'title' => 'EasyWeChat 4.0 入门指南',
                        'description' => 'EasyWeChat 4.0 于今天发布了，来看看新版用法',
                        'url' => 'http://easywechat.com/blog/4.0-tutorial.html',
                        'picurl' => 'http://img01.easywechat.com/4.0-tutorial.jpg',
                    ],
                ],
            ],
        ], $group->transformForJsonRequest());
    }

    public function testToXmlArray()
    {
        $group = new News([
            new NewsItem([
                'title' => 'EasyWeChat 4.0 发布了',
                'description' => 'EasyWeChat 4.0 于今天发布了',
                'url' => 'http://easywechat.com/blog/4.0-released.html',
                'image' => 'http://img01.easywechat.com/4.0.jpg',
            ]),
            new NewsItem([
                'title' => 'EasyWeChat 4.0 入门指南',
                'description' => 'EasyWeChat 4.0 于今天发布了，来看看新版用法',
                'url' => 'http://easywechat.com/blog/4.0-tutorial.html',
                'image' => 'http://img01.easywechat.com/4.0-tutorial.jpg',
            ]),
        ]);

        $this->assertSame([
            'ArticleCount' => 2,
            'Articles' => [
                [
                    'Title' => 'EasyWeChat 4.0 发布了',
                    'Description' => 'EasyWeChat 4.0 于今天发布了',
                    'Url' => 'http://easywechat.com/blog/4.0-released.html',
                    'PicUrl' => 'http://img01.easywechat.com/4.0.jpg',
                ],
                [
                    'Title' => 'EasyWeChat 4.0 入门指南',
                    'Description' => 'EasyWeChat 4.0 于今天发布了，来看看新版用法',
                    'Url' => 'http://easywechat.com/blog/4.0-tutorial.html',
                    'PicUrl' => 'http://img01.easywechat.com/4.0-tutorial.jpg',
                ],
            ],
        ], $group->toXmlArray());
    }
}
