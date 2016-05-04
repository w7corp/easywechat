<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\Message\Image;

class MessageImageTest extends TestCase
{
    /**
     * Test media().
     */
    public function testMedia()
    {
        $image = new Image();

        $return = $image->media('foobar');

        $this->assertEquals($image, $return);
        $this->assertEquals('foobar', $return->media_id);
    }
}
