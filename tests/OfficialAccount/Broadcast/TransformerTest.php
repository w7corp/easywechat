<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Broadcast;

use EasyWeChat\Applications\OfficialAccount\Broadcast\MessageTransformer;
use EasyWeChat\Tests\TestCase;

class TransformerTest extends TestCase
{
    /**
     * Test transform().
     */
    public function testTransform()
    {
        $transformer = new MessageTransformer('link', 'http://easywechat.org');

        $this->assertSame([], $transformer->transform());
    }

    /**
     * Test transformText().
     */
    public function testTransformText()
    {
        $transformer = new MessageTransformer('text', 'CONTENT');

        $msg = [
            'text' => [
                'content' => 'CONTENT',
            ],
            'msgtype' => 'text',
        ];

        $this->assertSame($msg, $transformer->transform());
    }

    /**
     * Test transformNews().
     */
    public function testTransformNews()
    {
        $transformer = new MessageTransformer('news', 'MEDIA_ID');

        $msg = [
            'mpnews' => [
                'media_id' => 'MEDIA_ID',
            ],
            'msgtype' => 'mpnews',
        ];

        $this->assertSame($msg, $transformer->transform());
    }

    /**
     * Test transformImage().
     */
    public function testTransformImage()
    {
        $transformer = new MessageTransformer('image', 'MEDIA_ID');

        $msg = [
            'image' => [
                'media_id' => 'MEDIA_ID',
            ],
            'msgtype' => 'image',
        ];

        $this->assertSame($msg, $transformer->transform());
    }

    /**
     * Test transformVideo().
     *
     * @expectedException \EasyWeChat\Exceptions\InvalidArgumentException
     */
    public function testTransformVideo()
    {
        $transformer = new MessageTransformer('video', ['MEDIA_ID', 'TITLE', 'DESCRIPTION']);

        $msg = [
            'mpvideo' => [
                'media_id' => 'MEDIA_ID',
                'title' => 'TITLE',
                'description' => 'DESCRIPTION',
            ],
            'msgtype' => 'mpvideo',
        ];

        $this->assertSame($msg, $transformer->transform());

        // exception
        (new MessageTransformer('video', ['MEDIA_ID', 'TITLE']))->transform();
    }

    /**
     * Test transformMpvideo().
     */
    public function testTransformMpvideo()
    {
        $transformer = new MessageTransformer('mpvideo', 'MEDIA_ID');

        $msg = [
            'mpvideo' => [
                'media_id' => 'MEDIA_ID',
            ],
            'msgtype' => 'mpvideo',
        ];

        $this->assertSame($msg, $transformer->transform());
    }

    /**
     * Test transformVoice().
     */
    public function testTransformVoice()
    {
        $transformer = new MessageTransformer('voice', 'MEDIA_ID');

        $msg = [
            'voice' => [
                'media_id' => 'MEDIA_ID',
            ],
            'msgtype' => 'voice',
        ];

        $this->assertSame($msg, $transformer->transform());
    }

    /**
     * Test transformCard().
     */
    public function testTransformCard()
    {
        $transformer = new MessageTransformer('card', 'CARD_ID');

        $msg = [
            'wxcard' => [
                'card_id' => 'CARD_ID',
            ],
            'msgtype' => 'wxcard',
        ];

        $this->assertSame($msg, $transformer->transform());
    }
}
