<?php

use EasyWeChat\Message\Voice;

class MessageVoiceTest extends TestCase
{
    /**
     * Test media()
     */
    public function testMedia()
    {
        $voice = new Voice();

        $return = $voice->media('foobar');

        $this->assertEquals($voice, $return);
        $this->assertEquals('foobar', $return->media_id);
    }
}