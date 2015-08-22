<?php

use EasyWeChat\Message\Image;

class MessageImageTest extends TestCase
{
    /**
     * Test media()
     */
    public function testMedia()
    {
        $image = new Image();

        $return = $image->media('foobar');

        $this->assertEquals($image, $return);
        $this->assertEquals('foobar', $return->media_id);
    }
}