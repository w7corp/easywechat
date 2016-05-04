<?php

/*
 * This file is part of the EasyWeChat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\Broadcast\Transformer;

class BroadcastTransformerTest extends TestCase
{
    /**
     * Test transform().
     */
    public function testTransform()
    {
        $transformer = new Transformer('link', 'http://easywechat.org');

        $this->assertEquals([], $transformer->transform());
    }

    /**
     * Test transformText().
     */
    public function testTransformText()
    {
        $transformer = new Transformer('text', 'CONTENT');

        $msg = [
            'text' => [
                'content' => 'CONTENT',
            ],
            'msgtype' => 'text',
        ];

        $this->assertEquals($msg, $transformer->transform());
    }

    /**
     * Test transformNews().
     */
    public function testTransformNews()
    {
        $transformer = new Transformer('news', 'MEDIA_ID');

        $msg = [
            'mpnews' => [
                'media_id' => 'MEDIA_ID',
            ],
            'msgtype' => 'mpnews',
        ];

        $this->assertEquals($msg, $transformer->transform());
    }

    /**
     * Test transformImage().
     */
    public function testTransformImage()
    {
        $transformer = new Transformer('image', 'MEDIA_ID');

        $msg = [
            'image' => [
                'media_id' => 'MEDIA_ID',
            ],
            'msgtype' => 'image',
        ];

        $this->assertEquals($msg, $transformer->transform());
    }

    /**
     * Test transformVideo().
     *
     * @expectedException EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function testTransformVideo()
    {
        $transformer = new Transformer('video', ['MEDIA_ID', 'TITLE', 'DESCRIPTION']);

        $msg = [
            'video' => [
                'media_id' => 'MEDIA_ID',
                'title' => 'TITLE',
                'description' => 'DESCRIPTION',
            ],
            'msgtype' => 'video',
        ];

        $this->assertEquals($msg, $transformer->transform());

        // exception
        (new Transformer('video', ['MEDIA_ID', 'TITLE']))->transform();
    }

    /**
     * Test transformMpvideo().
     */
    public function testTransformMpvideo()
    {
        $transformer = new Transformer('mpvideo', 'MEDIA_ID');

        $msg = [
            'mpvideo' => [
                'media_id' => 'MEDIA_ID',
            ],
            'msgtype' => 'mpvideo',
        ];

        $this->assertEquals($msg, $transformer->transform());
    }

    /**
     * Test transformVoice().
     */
    public function testTransformVoice()
    {
        $transformer = new Transformer('voice', 'MEDIA_ID');

        $msg = [
            'voice' => [
                'media_id' => 'MEDIA_ID',
            ],
            'msgtype' => 'voice',
        ];

        $this->assertEquals($msg, $transformer->transform());
    }

    /**
     * Test transformCard().
     */
    public function testTransformCard()
    {
        $transformer = new Transformer('card', 'CARD_ID');

        $msg = [
            'wxcard' => [
                'card_id' => 'CARD_ID',
            ],
            'msgtype' => 'wxcard',
        ];

        $this->assertEquals($msg, $transformer->transform());
    }
}
