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
use EasyWeChat\Message\News;
use EasyWeChat\Message\Text;
use EasyWeChat\Message\Transfer;
use EasyWeChat\Message\Video;
use EasyWeChat\Message\Voice;
use EasyWeChat\Server\Transformer;

class ServerTransformerTest extends TestCase
{
    /**
     * Test transformText().
     */
    public function testTransformText()
    {
        $transformer = new Transformer();

        $message = new Text(['content' => 'foo']);

        $this->assertEquals(['Content' => 'foo'], $transformer->transform($message));

        $this->assertEquals(['Content' => 'Text message.'], $transformer->transform('Text message.'));
    }

    /**
     * Test transformImage().
     */
    public function testTransformImage()
    {
        $transformer = new Transformer();

        $message = new Image(['media_id' => 'bar']);

        $this->assertEquals('bar', $transformer->transform($message)['Image']['MediaId']);
    }

    /**
     * Test transformVideo().
     */
    public function testTransformVideo()
    {
        $transformer = new Transformer();

        $message = new Video(['title' => 'overtrue']);

        $result = $transformer->transform($message);

        $this->assertEquals('overtrue', $result['Video']['Title']);
        $this->assertArrayHasKey('Description', $result['Video']);
        $this->assertArrayHasKey('MediaId', $result['Video']);
        $this->assertArrayNotHasKey('ThumbMediaId', $result['Video']);
    }

    /**
     * Test transformVioce().
     */
    public function testTransformVoice()
    {
        $transformer = new Transformer();

        $message = new Voice(['media_id' => 'bar']);

        $this->assertEquals('bar', $transformer->transform($message)['Voice']['MediaId']);
    }

    /**
     * Test transformTransfer().
     */
    public function testTransformTransfer()
    {
        $transformer = new Transformer();

        $message = new Transfer(['account' => 'foo@bar.com']);

        $this->assertEquals('foo@bar.com', $transformer->transform($message)['TransInfo']['KfAccount']);
    }

    /**
     * Test transformNews().
     */
    public function testTransformNews()
    {
        $transformer = new Transformer();

        // one
        $result = $transformer->transform(new News([
                'author' => 'overtrue',
                'description' => 'foobar',
            ]));

        $this->assertEquals(1, $result['ArticleCount']);
        $this->assertEquals(1, count($result['Articles']));
        $this->assertEquals('foobar', $result['Articles'][0]['Description']);
        $this->assertArrayHasKey('Url', $result['Articles'][0]);
        $this->assertArrayNotHasKey('Author', $result['Articles'][0]);

        // more
        $articles = [
            new News([
                'author' => 'overtrue',
                'description' => 'foobar',
            ]),
            new News([
                'author' => 'foo',
                'description' => 'bar',
            ]),
        ];

        $result = $transformer->transform($articles);

        $this->assertEquals(2, $result['ArticleCount']);
        $this->assertEquals(2, count($result['Articles']));
        $this->assertEquals('foobar', $result['Articles'][0]['Description']);
        $this->assertEquals('bar', $result['Articles'][1]['Description']);
        $this->assertArrayHasKey('Url', $result['Articles'][0]);
        $this->assertArrayNotHasKey('Author', $result['Articles'][0]);
    }
}
