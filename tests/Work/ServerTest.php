<?php

namespace EasyWeChat\Tests\Work;

use EasyWeChat\Kernel\Support\Xml;
use EasyWeChat\Work\Account;
use EasyWeChat\Work\Server;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class ServerTest extends TestCase
{
    public function test_it_will_handle_validation_request()
    {
        $request = (new ServerRequest('GET', 'http://easywechat.com/?echostr=abcdefghijklmn'))->withQueryParams(['echostr' => 'abcdefghijklmn']);
        $server = new Server(\Mockery::mock(Account::class), $request);

        $response = $server->serve();

        $this->assertSame('abcdefghijklmn', \strval($response->getBody()));
    }

    public function test_it_will_response_success_without_handlers()
    {
        $body = '<xml>
           <Encrypt><![CDATA[msg_encrypt]]></Encrypt>
           <MsgSignature><![CDATA[msg_signature]]></MsgSignature>
           <TimeStamp>timestamp</TimeStamp>
           <Nonce><![CDATA[nonce]]></Nonce>
        </xml>';
        $request = (new ServerRequest('POST', 'http://easywechat.com/server', [], $body));
        $server = new Server(\Mockery::mock(Account::class), $request);

        $response = $server->serve();

        $this->assertSame('SUCCESS', \strval($response->getBody()));
    }

    public function test_it_will_respond_from_message_handlers()
    {
        $body = '<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[sys]]></FromUserName> 
            <CreateTime>1403610513</CreateTime>
            <MsgType><![CDATA[event]]></MsgType>
            <Event><![CDATA[change_contact]]></Event>
            <ChangeType>delete_user</ChangeType>
            <UserID><![CDATA[zhangsan]]></UserID>
        </xml>';
        $request = (new ServerRequest('POST', 'http://easywechat.com/server', [], $body));
        $server = new Server(\Mockery::mock(Account::class), $request);

        $response = $server
            ->addMessageListener(
                'event',
                function ($message) {
                    return 'hello';
                }
            )
            ->addEventListener(
                'scancode_push',
                function ($message) {
                    return 'world';
                }
            )
            ->serve();

        $response = Xml::parse(\strval($response->getBody()));

        $this->assertSame('sys', $response['ToUserName']);
        $this->assertSame('toUser', $response['FromUserName']);
        $this->assertSame('text', $response['MsgType']);
        $this->assertSame('hello', $response['Content']);
    }

    public function test_it_will_respond_from_event_handlers()
    {
        $body = '<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[sys]]></FromUserName> 
            <CreateTime>1403610513</CreateTime>
            <MsgType><![CDATA[event]]></MsgType>
            <Event><![CDATA[change_contact]]></Event>
            <ChangeType>delete_user</ChangeType>
            <UserID><![CDATA[zhangsan]]></UserID>
        </xml>';
        $request = (new ServerRequest('POST', 'http://easywechat.com/server', [], $body));
        $server = new Server(\Mockery::mock(Account::class), $request);

        $response = $server
            ->addMessageListener(
                'event',
                function ($message) {
                    return 'hello';
                }
            )
            ->addEventListener(
                'change_contact',
                function ($message) {
                    return 'world';
                }
            )
            ->serve();

        $response = Xml::parse(\strval($response->getBody()));

        $this->assertSame('sys', $response['ToUserName']);
        $this->assertSame('toUser', $response['FromUserName']);
        $this->assertSame('text', $response['MsgType']);
        $this->assertSame('world', $response['Content']);
    }
}
