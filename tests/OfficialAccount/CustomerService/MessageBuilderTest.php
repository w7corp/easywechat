<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\CustomerService;

use EasyWeChat\Applications\OfficialAccount\CustomerService\Messenger;
use EasyWeChat\Applications\OfficialAccount\Message\Raw;
use EasyWeChat\Applications\OfficialAccount\Message\Text;
use EasyWeChat\Tests\TestCase;

class MessageBuilderTest extends TestCase
{
    public function getMessageBuilder()
    {
        $customerService = \Mockery::mock('EasyWeChat\Applications\OfficialAccount\CustomerService\Client');
        $customerService->shouldReceive('send')->andReturnUsing(function ($message) {
            return $message;
        });

        return new Messenger($customerService);
    }

    /**
     * Test message().
     */
    public function testMessage()
    {
        $MessageBuilder = $this->getMessageBuilder();

        $response = $MessageBuilder->message('hello');

        $this->assertSame($MessageBuilder, $response);
        $this->assertInstanceOf(Text::class, $MessageBuilder->message);
    }

    /**
     * Test by().
     */
    public function testBy()
    {
        $MessageBuilder = $this->getMessageBuilder();

        $response = $MessageBuilder->by('hello');

        $this->assertSame($MessageBuilder, $response);
        $this->assertSame('hello', $MessageBuilder->account);
        $this->assertNull($MessageBuilder->by);
    }

    /**
     * Test to().
     */
    public function testTo()
    {
        $MessageBuilder = $this->getMessageBuilder();

        $response = $MessageBuilder->to('overtrue');

        $this->assertSame($MessageBuilder, $response);
        $this->assertSame('overtrue', $MessageBuilder->to);
    }

    /**
     * Test send().
     *
     * @expectedException \EasyWeChat\Exceptions\RuntimeException
     */
    public function testSend()
    {
        $MessageBuilder = $this->getMessageBuilder();

        $response = $MessageBuilder->message('hello')->by('overtrue')->to('easywechat')->send();

        $this->assertSame('text', $response['msgtype']);
        $this->assertSame('hello', $response['text']['content']);
        $this->assertSame('overtrue', $response['customservice']['kf_account']);
        $this->assertSame('easywechat', $response['touser']);

        // exception
        $MessageBuilder = $this->getMessageBuilder();
        $MessageBuilder->by('overtrue')->to('easywechat')->send();
    }

    /**
     * Test message() with raw message.
     */
    public function testRawMessage()
    {
        $MessageBuilder = $this->getMessageBuilder();
        $string = '{
            "touser":"OPENID",
            "msgtype":"text",
            "text":
            {
                 "content":"Hello World"
            }
        }';
        $message = new Raw($string);
        $response = $MessageBuilder->message($message)->send();

        $this->assertSame($string, $response);
    }
}
