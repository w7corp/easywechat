<?php

/*
 * This file is part of the EasyWeChat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\Broadcast\Broadcast;
use EasyWeChat\Broadcast\MessageBuilder;

class BroadcastMessageBuilderTest extends TestCase
{
    /**
     * Test msgType().
     *
     * @expectedException EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function testMsgType()
    {
        $messageBuilder = new MessageBuilder();

        $response = $messageBuilder->msgType(Broadcast::MSG_TYPE_TEXT);

        $this->assertEquals($messageBuilder, $response);
        $this->assertEquals(Broadcast::MSG_TYPE_TEXT, $messageBuilder->msgType);

        // exception
        $messageBuilder->msgType('link');
    }

    /**
     * Test message();.
     */
    public function testMessage()
    {
        $messageBuilder = new MessageBuilder();

        $response = $messageBuilder->message('CONTENT');

        $this->assertEquals($messageBuilder, $response);
        $this->assertEquals('CONTENT', $messageBuilder->message);
    }

    /**
     * Test to().
     */
    public function testTo()
    {
        $messageBuilder = new MessageBuilder();

        $response = $messageBuilder->to('GROUP');

        $this->assertEquals($messageBuilder, $response);
        $this->assertEquals('GROUP', $messageBuilder->to);
    }

    /**
     * Test build().
     *
     * @expectedException EasyWeChat\Core\Exceptions\RuntimeException
     */
    public function testBuild()
    {
        $messageBuilder = new MessageBuilder();
        $messageBuilder->msgType(Broadcast::MSG_TYPE_TEXT)->message('CONTENT');
        $message = [
            'filter' => [
                'is_to_all' => true,
            ],
            'text' => [
                'content' => 'CONTENT',
            ],
            'msgtype' => 'text',
        ];
        $this->assertEquals($message, $messageBuilder->build());

        $messageBuilder = new MessageBuilder();
        $messageBuilder->msgType(Broadcast::MSG_TYPE_VIDEO)->message('MEDIA_ID');
        $message = [
            'filter' => [
                'is_to_all' => true,
            ],
            'mpvideo' => [
                'media_id' => 'MEDIA_ID',
            ],
            'msgtype' => 'mpvideo',
        ];
        $this->assertEquals($message, $messageBuilder->build());

        $messageBuilder = new MessageBuilder();
        $messageBuilder->msgType(Broadcast::MSG_TYPE_VIDEO)->message(['MEDIA_ID', 'TITLE', 'DESCRIPTION'])->to(['OPENID1', 'OPENID2', 'OPENID3']);
        $message = [
            'touser' => [
                'OPENID1',
                'OPENID2',
                'OPENID3',
            ],
            'video' => [
                'media_id' => 'MEDIA_ID',
                'title' => 'TITLE',
                'description' => 'DESCRIPTION',
            ],
            'msgtype' => 'video',
        ];
        $this->assertEquals($message, $messageBuilder->build());

        // exception
        $messageBuilder = new MessageBuilder();
        $messageBuilder->build();
    }

    /**
     * Test buildPreview().
     *
     * @expectedException EasyWeChat\Core\Exceptions\RuntimeException
     * @expectedException EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function testBuildPreview()
    {
        $messageBuilder = new MessageBuilder();
        $messageBuilder->msgType(Broadcast::MSG_TYPE_TEXT)->message('CONTENT')->to('OPENID');
        $message = [
            Broadcast::PREVIEW_BY_OPENID => 'OPENID',
            'text' => [
                'content' => 'CONTENT',
            ],
            'msgtype' => 'text',
        ];
        $this->assertEquals($message, $messageBuilder->buildPreview(Broadcast::PREVIEW_BY_OPENID));

        $messageBuilder = new MessageBuilder();
        $messageBuilder->msgType(Broadcast::MSG_TYPE_TEXT)->message('CONTENT')->to('WXH');
        $message = [
            Broadcast::PREVIEW_BY_NAME => 'WXH',
            'text' => [
                'content' => 'CONTENT',
            ],
            'msgtype' => 'text',
        ];
        $this->assertEquals($message, $messageBuilder->buildPreview(Broadcast::PREVIEW_BY_NAME));

        // exception
        $messageBuilder = new MessageBuilder();
        $messageBuilder->build();
    }
}
