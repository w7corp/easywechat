<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Message;

use EasyWeChat\Message\Image;
use EasyWeChat\Tests\TestCase;

class MessageImageTest extends TestCase
{
    /**
     * Test media().
     */
    public function testMedia()
    {
        $image = new Image();

        $return = $image->media('foobar');

        $this->assertSame($image, $return);
        $this->assertSame('foobar', $return->media_id);
    }
}
