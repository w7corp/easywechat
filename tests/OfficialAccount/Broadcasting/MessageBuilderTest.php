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
        ], $builder->message(new Text('hello world!'))->to('mock-openid')->buildForPreview(Client::PREVIEW_BY_OPENID));

        $this->assertSame([
            'towxname' => 'mock-username',
            'msgtype' => 'text',
            'text' => [
                'content' => 'hello world!',
            ],
        ], $builder->message(new Text('hello world!'))->to('mock-username')->buildForPreview(Client::PREVIEW_BY_NAME));
    }

    public function testBuildGroup()
    {
        $builder = new MessageBuilder();

        $text = new Text('hello world!');

        // without to
        $message = $builder->message($text)->build();

        $this->assertSame([
            'filter' => [
                'is_to_all' => true,
            ],
            'msgtype' => 'text',
            'text' => [
                'content' => 'hello world!',
            ],
        ], $message);

        // with single group
        $message = $builder->message($text)->to('mock-group-id')->build();

        $this->assertSame([
            'filter' => [
                'is_to_all' => false,
                'group_id' => 'mock-group-id',
            ],
            'msgtype' => 'text',
            'text' => [
                'content' => 'hello world!',
            ],
        ], $message);

        // with multi group
        $message = $builder->message($text)->to(['mock-group-id1', 'mock-group-id2'])->build();

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
