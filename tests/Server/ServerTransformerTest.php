<?php

use EasyWeChat\Message\Text;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Video;
use EasyWeChat\Message\Voice;
use EasyWeChat\Message\Transfer;
use EasyWeChat\Message\Article;
use EasyWeChat\Message\Articles;
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
     * Test transformArticles().
     */
    public function testTransformArticles()
    {
        $transformer = new Transformer();

        $articles = [
            new Article([
                'author' => 'overtrue',
                'description' => 'foobar',
            ]),
            new Article([
                'author' => 'foo',
                'description' => 'bar',
            ]),
        ];

        $message = new Articles($articles);

        $result = $transformer->transform($message);

        $this->assertEquals(2, $result['ArticleCount']);
        $this->assertEquals(2, count($result['Articles']));
        $this->assertEquals('foobar', $result['Articles'][0]['Description']);
        $this->assertArrayHasKey('Url', $result['Articles'][0]);
        $this->assertArrayNotHasKey('Author', $result['Articles'][0]);
    }
}
