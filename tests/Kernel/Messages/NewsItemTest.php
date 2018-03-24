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

use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Tests\TestCase;

class NewsItemTest extends TestCase
{
    public function testToXmlArray()
    {
        $message = new NewsItem([
                'title' => 'EasyWeChat 4.0 发布了',
                'description' => 'EasyWeChat 4.0 于今天发布了',
                'url' => 'http://easywechat.com/blog/4.0-released.html',
                'image' => 'http://img01.easywechat.com/4.0.jpg',
            ]);

        $this->assertSame([
            'Title' => 'EasyWeChat 4.0 发布了',
            'Description' => 'EasyWeChat 4.0 于今天发布了',
            'Url' => 'http://easywechat.com/blog/4.0-released.html',
            'PicUrl' => 'http://img01.easywechat.com/4.0.jpg',
        ], $message->toXmlArray());
    }
}
