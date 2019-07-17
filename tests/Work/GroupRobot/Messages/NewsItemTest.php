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
use EasyWeChat\Work\GroupRobot\Messages\NewsItem;

class NewsItemTest extends TestCase
{
    public function testBasicFeatures()
    {
        $message = new NewsItem([
            'title' => 'mock-title',
            'description' => 'mock-description',
            'url' => 'mock-url',
            'image' => 'mock-image',
        ]);

        $this->assertSame([
            'title' => 'mock-title',
            'description' => 'mock-description',
            'url' => 'mock-url',
            'picurl' => 'mock-image',
        ], $message->toJsonArray());

        $this->assertInstanceOf(Message::class, $message);
    }
}
