<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\Message\Video;

class MessageVideoTest extends TestCase
{
    /**
     * Test media().
     */
    public function testMedia()
    {
        $video = new Video();

        $return = $video->media('foobar');

        $this->assertEquals($video, $return);
        $this->assertEquals('foobar', $return->media_id);
    }

    /**
     * Test thumb().
     */
    public function testThumb()
    {
        $video = new Video();

        $return = $video->thumb('thumbFoo');

        $this->assertEquals($video, $return);
        $this->assertEquals('thumbFoo', $return->thumb_media_id);
    }
}
