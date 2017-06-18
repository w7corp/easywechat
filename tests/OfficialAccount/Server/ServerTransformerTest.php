<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Server;

use EasyWeChat\Applications\OfficialAccount\Message\Image;
use EasyWeChat\Applications\OfficialAccount\Message\News;
use EasyWeChat\Applications\OfficialAccount\Message\Text;
use EasyWeChat\Applications\OfficialAccount\Message\Transfer;
use EasyWeChat\Applications\OfficialAccount\Message\Video;
use EasyWeChat\Applications\OfficialAccount\Message\Voice;
use EasyWeChat\Applications\OfficialAccount\Server\MessageTransformer;
use EasyWeChat\Tests\TestCase;

class ServerTransformerTest extends TestCase
{
    /**
     * Test transformText().
     */
    public function testTransformText()
    {
        $transformer = new MessageTransformer();

        $message = new Text(['content' => 'foo']);

        $this->assertSame(['Content' => 'foo'], $transformer->transform($message));

        $this->assertSame(['Content' => 'Text message.'], $transformer->transform('Text message.'));
    }

    /**
     * Test transformImage().
     */
    public function testTransformImage()
    {
        $transformer = new MessageTransformer();

        $message = new Image(['media_id' => 'bar']);

        $this->assertSame('bar', $transformer->transform($message)['Image']['MediaId']);
    }

    /**
     * Test transformVideo().
     */
    public function testTransformVideo()
    {
        $transformer = new MessageTransformer();

        $message = new Video(['title' => 'overtrue']);

        $result = $transformer->transform($message);

        $this->assertSame('overtrue', $result['Video']['Title']);
        $this->assertArrayHasKey('Description', $result['Video']);
        $this->assertArrayHasKey('MediaId', $result['Video']);
        $this->assertArrayNotHasKey('ThumbMediaId', $result['Video']);
    }

    /**
     * Test transformVioce().
     */
    public function testTransformVoice()
    {
        $transformer = new MessageTransformer();

        $message = new Voice(['media_id' => 'bar']);

        $this->assertSame('bar', $transformer->transform($message)['Voice']['MediaId']);
    }

    /**
     * Test transformTransfer().
     */
    public function testTransformTransfer()
    {
        $transformer = new MessageTransformer();

        $message = new Transfer(['account' => 'foo@bar.com']);

        $this->assertSame('foo@bar.com', $transformer->transform($message)['TransInfo']['KfAccount']);
    }

    /**
     * Test transformNews().
     */
    public function testTransformNews()
    {
        $transformer = new MessageTransformer();

        // one
        $result = $transformer->transform(new News([
                'author' => 'overtrue',
                'description' => 'foobar',
            ]));

        $this->assertSame(1, $result['ArticleCount']);
        $this->assertSame(1, count($result['Articles']));
        $this->assertSame('foobar', $result['Articles'][0]['Description']);
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

        $this->assertSame(2, $result['ArticleCount']);
        $this->assertSame(2, count($result['Articles']));
        $this->assertSame('foobar', $result['Articles'][0]['Description']);
        $this->assertSame('bar', $result['Articles'][1]['Description']);
        $this->assertArrayHasKey('Url', $result['Articles'][0]);
        $this->assertArrayNotHasKey('Author', $result['Articles'][0]);
    }
}
