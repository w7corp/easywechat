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
use EasyWeChat\Work\GroupRobot\Messages\Image;
use EasyWeChat\Work\GroupRobot\Messages\Message;

class ImageTest extends TestCase
{
    public function testBasicFeatures()
    {
        $image = new Image('mock-base64', 'mock-md5');

        $this->assertSame('mock-base64', $image->base64);
        $this->assertSame('mock-md5', $image->md5);
        $this->assertInstanceOf(Message::class, $image);
    }
}
