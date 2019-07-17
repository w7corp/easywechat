<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\GroupRobot\Messages;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\GroupRobot\Messages\Markdown;
use EasyWeChat\Work\GroupRobot\Messages\Message;

class MarkdownTest extends TestCase
{
    public function testBasicFeatures()
    {
        $markdown = new Markdown('mock-content');

        $this->assertSame('mock-content', $markdown->content);
        $this->assertInstanceOf(Message::class, $markdown);
    }
}
