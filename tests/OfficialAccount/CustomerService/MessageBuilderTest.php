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

use EasyWeChat\OfficialAccount\CustomerService\MessageBuilder;
use EasyWeChat\OfficialAccount\Message\Raw;
use EasyWeChat\OfficialAccount\Message\Text;
use EasyWeChat\Tests\TestCase;

class MessageBuilderTest extends TestCase
{
    public function getMessageBuilder()
    {
        $customerService = \Mockery::mock('EasyWeChat\OfficialAccount\CustomerService\Client');
        $customerService->shouldReceive('send')->andReturnUsing(function ($message) {
            return $message;
        });

        return new MessageBuilder($customerService);
    }

    /**
     * Test message().
     */
    public function testMessage()
    {
        $MessageBuilder = $this->getMessageBuilder();

        $response = $MessageBuilder->message('hello');

        $this->assertEquals($MessageBuilder, $response);
        $this->assertInstanceOf(Text::class, $MessageBuilder->message);
    }

    /**
     * Test by().
     */
    public function testBy()
    {
        $MessageBuilder = $this->getMessageBuilder();

        $response = $MessageBuilder->by('hello');

        $this->assertEquals($MessageBuilder, $response);
        $this->assertEquals('hello', $MessageBuilder->account);
        $this->assertNull($MessageBuilder->by);
    }

    /**
     * Test to().
     */
    public function testTo()
    {
        $MessageBuilder = $this->getMessageBuilder();

        $response = $MessageBuilder->to('overtrue');

        $this->assertEquals($MessageBuilder, $response);
        $this->assertEquals('overtrue', $MessageBuilder->to);
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

        $this->assertEquals('text', $response['msgtype']);
        $this->assertEquals('hello', $response['text']['content']);
        $this->assertEquals('overtrue', $response['customservice']['kf_account']);
        $this->assertEquals('easywechat', $response['touser']);

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

        $this->assertEquals($string, $response);
    }
}
