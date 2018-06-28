<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Broadcasting;

use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\OfficialAccount\Broadcasting\Client;
use EasyWeChat\OfficialAccount\Broadcasting\MessageBuilder;
use EasyWeChat\Tests\TestCase;

class MessageBuilderTest extends TestCase
{
    public function testMessageBuildWithoutMessage()
    {
        $builder = new MessageBuilder();

        // without message
        try {
            $builder->build();
            $this->fail('Faild to assert Exception thrown.');
        } catch (\Exception $e) {
            $this->assertSame('No message content to send.', $e->getMessage());
        }
    }

    public function testBuildForPreview()
    {
        $builder = new MessageBuilder();

        $this->assertSame([
            'touser' => 'mock-openid',
            'msgtype' => 'text',
            'text' => [
                'content' => 'hello world!',
            ],
        ], $builder->message(new Text('hello world!'))->buildForPreview(Client::PREVIEW_BY_OPENID, 'mock-openid'));

        $this->assertSame([
            'towxname' => 'mock-username',
            'msgtype' => 'text',
            'text' => [
                'content' => 'hello world!',
            ],
        ], $builder->message(new Text('hello world!'))->buildForPreview(Client::PREVIEW_BY_NAME, 'mock-username'));
    }

    public function testBuild()
    {
        $text = new Text('hello world!');

        // to all
        $message = (new MessageBuilder())->message($text)->toAll()->build();

        $this->assertSame([
            'filter' => [
                'is_to_all' => true,
            ],
            'msgtype' => 'text',
            'text' => [
                'content' => 'hello world!',
            ],
        ], $message);

        // to tag
        $message = (new MessageBuilder())->message($text)->toTag(23)->build();

        $this->assertSame([
            'filter' => [
                'is_to_all' => false,
                'tag_id' => 23,
            ],
            'msgtype' => 'text',
            'text' => [
                'content' => 'hello world!',
            ],
        ], $message);

        // to users
        $message = (new MessageBuilder())->message($text)->toUsers(['mock-group-id1', 'mock-group-id2'])->build();

        $this->assertSame([
            'touser' => [
                'mock-group-id1',
                'mock-group-id2',
            ],
            'msgtype' => 'text',
            'text' => [
                'content' => 'hello world!',
            ],
        ], $message);
    }
}
