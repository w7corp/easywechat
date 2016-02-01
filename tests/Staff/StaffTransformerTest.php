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
use EasyWeChat\Message\Link;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Text;
use EasyWeChat\Message\Video;
use EasyWeChat\Message\Voice;
use EasyWeChat\Staff\Transformer;

class StaffTransformerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test transform().
     */
    public function testTransform()
    {
        $message = Mockery::mock(Link::class);
        $transformer = new Transformer();

        $this->assertEquals([], $transformer->transform($message));
    }

    /**
     * Test transformText().
     */
    public function testTransformText()
    {
        $message = new Text();
        $message->content = 'foo';

        $transformer = new Transformer();

        $this->assertEquals(['msgtype' => 'text', 'text' => ['content' => 'foo']], $transformer->transform($message));
    }

    /**
     * Test transformImage().
     */
    public function testTransformImage()
    {
        $message = new Image();
        $message->media_id = 'foo';

        $transformer = new Transformer();

        $this->assertEquals(['msgtype' => 'image', 'image' => ['media_id' => 'foo']], $transformer->transform($message));
    }

    /**
     * Test transformVideo().
     */
    public function testTransformVideo()
    {
        $message = new Video();
        $message->media_id = 'foo';
        $message->title = 'hello world';
        $message->description = 'description string.';
        $message->thumb_media_id = 'thumb media id';

        $transformer = new Transformer();

        $result = $transformer->transform($message);
        $this->assertEquals('video', $result['msgtype']);
        $this->assertEquals('foo', $result['video']['media_id']);
        $this->assertEquals('hello world', $result['video']['title']);
        $this->assertEquals('description string.', $result['video']['description']);
        $this->assertEquals('thumb media id', $result['video']['thumb_media_id']);
    }

    /**
     * Test transformVoice().
     */
    public function testTransformVoice()
    {
        $message = new Voice();
        $message->media_id = 'foo';

        $transformer = new Transformer();

        $this->assertEquals(['msgtype' => 'voice', 'voice' => ['media_id' => 'foo']], $transformer->transform($message));
    }

    /**
     * Test transformNews().
     */
    public function testTransformNews()
    {
        $transformer = new Transformer();

        // one
        $result = $transformer->transform(new News([
                'title' => 'overtrue',
                'description' => 'foobar',
            ]));

        $this->assertEquals('news', $result['msgtype']);
        $this->assertEquals('overtrue', $result['news']['articles'][0]['title']);
        $this->assertEquals('foobar', $result['news']['articles'][0]['description']);

        // more
        $news = [
                new News(['title' => 'foo']),
                new News(['title' => 'bar']),
        ];

        $result = $transformer->transform($news);
        $this->assertEquals('news', $result['msgtype']);
        $this->assertEquals('foo', $result['news']['articles'][0]['title']);
        $this->assertEquals('bar', $result['news']['articles'][1]['title']);
    }
}
