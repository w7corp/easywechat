<?php

use EasyWeChat\Message\Video;

class MessageVideoTest extends TestCase
{
    /**
     * Test media()
     */
    public function testMedia()
    {
        $video = new Video();

        $return = $video->media('foobar');

        $this->assertEquals($video, $return);
        $this->assertEquals('foobar', $return->media_id);
    }

    /**
     * Test thumb()
     */
    public function testThumb()
    {
        $video = new Video();

        $return = $video->thumb('thumbFoo');

        $this->assertEquals($video, $return);
        $this->assertEquals('thumbFoo', $return->thumb_media_id);
    }
}