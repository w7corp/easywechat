<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Kernel\Messages;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\Messages\Message;
use EasyWeChat\Kernel\Support\XML;
use EasyWeChat\Tests\TestCase;

class MessageTest extends TestCase
{
    public function testToXmlArrayException()
    {
        $message = new class() extends Message {
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf('Class "%s" cannot support transform to XML message.', Message::class));
        $message->toXmlArray();
    }

    public function testBasicFeatures()
    {
        $message = new DummyMessageForMessageTest([
                'media_id' => '12345',
            ]);
        $this->assertSame('dummy', $message->getType());
        $this->assertSame('12345', $message->media_id);
        $this->assertSame('12345', $message->get('media_id'));
        $this->assertSame('12345', $message->getAttribute('media_id'));

        $this->assertEmpty($message->to);

        // type
        $message->setType('new-type');
        $this->assertSame('new-type', $message->getType());

        // setter
        $message->to = ['touser' => 'mock-openid'];
        $this->assertSame(['touser' => 'mock-openid'], $message->to);

        // attributes
        $message->new_attribute = 'mock-content';
        $this->assertSame('mock-content', $message->new_attribute);
    }

    public function testTransformForJsonRequest()
    {
        // required
        $message = new DummyMessageForMessageTest([
            'media_id' => '12345',
            'foo' => 'f',
            'bar' => 'b',
            'required_id' => 'r',
            'title' => null,
        ]);

        $this->assertSame([
            'msgtype' => 'dummy',
            'append_id' => 'ap_id',
            'dummy' => [
                'media_id' => '12345',
                'foo_id' => 'f',
                'bar_name' => 'b',
                'required_id' => 'r',
            ],
        ], $message->transformForJsonRequest(['append_id' => 'ap_id']));

        // optional
        $message = new DummyMessageForMessageTest([
            'media_id' => '12345',
            'title' => 'the title',
            'foo' => 'f',
            'bar' => 'b',
            'required_id' => 'r',
        ]);

        $this->assertSame([
            'msgtype' => 'dummy',
            'append_id' => 'ap_id',
            'dummy' => [
                'media_id' => '12345',
                'title' => 'the title',
                'foo_id' => 'f',
                'bar_name' => 'b',
                'required_id' => 'r',
            ],
        ], $message->transformForJsonRequest(['append_id' => 'ap_id']));
    }

    public function testTransformToXml()
    {
        // required
        $message = new DummyMessageForMessageTest([
            'media_id' => '12345',
            'foo' => 'f',
            'bar' => 'b',
            'required_id' => 'r',
            'title' => null,
        ]);

        $this->assertSame(XML::build([
            'MsgType' => 'dummy',
            'MediaId' => 12345,
            'RequiredId' => 'r',
            'append_id' => 'ap_id',
        ]), $message->transformToXml(['append_id' => 'ap_id']));
    }

    public function testMissingRequiredKey()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"required_id" cannot be empty.');
        $message = new DummyMessageForMessageTest([
            'media_id' => '12345',
            'foo' => 'f',
            'bar' => 'b',
        ]);
        $message->transformForJsonRequest();
    }
}

class DummyMessageForMessageTest extends Message
{
    protected $type = 'dummy';

    protected $properties = [
        'foo',
        'bar',
        'media_id',
        'title',
        'required_id',
    ];

    protected $jsonAliases = [
        'foo_id' => 'foo',
        'bar_name' => 'bar',
    ];

    protected $xmlAliases = [
        'Foo' => 'foo',
        'Bar' => 'bar',
    ];

    protected $required = [
        'media_id', 'required_id',
    ];

    public function toXmlArray()
    {
        return [
            'MediaId' => $this->get('media_id'),
            'RequiredId' => $this->get('required_id'),
        ];
    }
}
