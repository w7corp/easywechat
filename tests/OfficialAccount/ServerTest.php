<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\OfficialAccount;

use EasyWeChat\Kernel\Support\Xml;
use EasyWeChat\OfficialAccount\Server;
use EasyWeChat\Tests\TestCase;
use Nyholm\Psr7\ServerRequest;

class ServerTest extends TestCase
{
    public function test_it_will_handle_validation_request()
    {
        $request = (new ServerRequest('GET', 'http://easywechat.com/?echostr=abcdefghijklmn'))->withQueryParams(['echostr' => 'abcdefghijklmn']);
        $server = new Server($request);

        $response = $server->serve();

        $this->assertSame('abcdefghijklmn', \strval($response->getBody()));
    }

    public function test_it_will_response_success_without_handlers()
    {
        $body = '<xml>
          <ToUserName><![CDATA[toUser]]></ToUserName>
          <FromUserName><![CDATA[fromUser]]></FromUserName>
          <CreateTime>1348831860</CreateTime>
          <MsgType><![CDATA[text]]></MsgType>
          <Content><![CDATA[this is a test]]></Content>
          <MsgId>1234567890123456</MsgId>
        </xml>';
        $request = (new ServerRequest('POST', 'http://easywechat.com/server', [], $body));
        $server = new Server($request);

        $response = $server->serve();

        $this->assertSame('success', \strval($response->getBody()));
    }

    public function test_it_will_respond_from_message_handlers()
    {
        $body = '<xml>
          <ToUserName><![CDATA[toUser]]></ToUserName>
          <FromUserName><![CDATA[fromUser]]></FromUserName>
          <CreateTime>1348831860</CreateTime>
          <MsgType><![CDATA[text]]></MsgType>
          <Content><![CDATA[this is a test]]></Content>
          <MsgId>1234567890123456</MsgId>
        </xml>';
        $request = (new ServerRequest('POST', 'http://easywechat.com/server', [], $body));
        $server = new Server($request);

        $response = $server
            ->addMessageListener(
                'text',
                function ($message) {
                    return 'hello';
                }
            )->addEventListener(
                'subscribe',
                function ($message) {
                    return 'world';
                }
            )->serve();

        $response = Xml::parse(\strval($response->getBody()));

        $this->assertSame('toUser', $response['FromUserName']);
        $this->assertSame('fromUser', $response['ToUserName']);
        $this->assertSame('text', $response['MsgType']);
        $this->assertSame('hello', $response['Content']);
    }

    public function test_it_will_respond_from_event_handlers()
    {
        $body = '<xml>
          <ToUserName><![CDATA[toUser]]></ToUserName>
          <FromUserName><![CDATA[fromUser]]></FromUserName>
          <CreateTime>123456789</CreateTime>
          <MsgType><![CDATA[event]]></MsgType>
          <Event><![CDATA[subscribe]]></Event>
          <EventKey><![CDATA[qrscene_123123]]></EventKey>
          <Ticket><![CDATA[TICKET]]></Ticket>
        </xml>';
        $request = (new ServerRequest('POST', 'http://easywechat.com/server', [], $body));
        $server = new Server($request);

        $response = $server
            ->addMessageListener(
                'text',
                function ($message) {
                    return 'hello';
                }
            )->addEventListener(
                'subscribe',
                function ($message) {
                    return 'world';
                }
            )->serve();

        $response = Xml::parse(\strval($response->getBody()));

        $this->assertSame('toUser', $response['FromUserName']);
        $this->assertSame('fromUser', $response['ToUserName']);
        $this->assertSame('text', $response['MsgType']);
        $this->assertSame('world', $response['Content']);
    }
}
