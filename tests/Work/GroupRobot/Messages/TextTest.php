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
use EasyWeChat\Work\GroupRobot\Messages\Message;
use EasyWeChat\Work\GroupRobot\Messages\Text;

class TextTest extends TestCase
{
    public function testBasicFeatures()
    {
        $text = new Text('mock-content');

        $this->assertSame('mock-content', $text->content);
        $this->assertInstanceOf(Message::class, $text);
    }

    public function testMentionWithConstruct()
    {
        $text = new Text('mock-content', 'mock-userid', 'mock-mobile');

        $this->assertSame('mock-content', $text->content);
        $this->assertSame(['mock-userid'], $text->mentioned_list);
        $this->assertSame(['mock-mobile'], $text->mentioned_mobile_list);
    }

    public function testMentionWithMethod()
    {
        $text = new Text('mock-content');

        $text->mention('mock-userid');
        $text->mentionByMobile('mock-mobile');

        $this->assertSame('mock-content', $text->content);
        $this->assertSame(['mock-userid'], $text->mentioned_list);
        $this->assertSame(['mock-mobile'], $text->mentioned_mobile_list);
    }
}
