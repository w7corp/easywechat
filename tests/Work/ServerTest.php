<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Work;

use EasyWeChat\Kernel\Support\Xml;
use EasyWeChat\Work\Account;
use EasyWeChat\Work\Application;
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

    public function test_it_will_validate_message()
    {
        $body = '<xml>
                <ToUserName><![CDATA[wx5823bf96d3bd56c7]]></ToUserName>
                <Encrypt><![CDATA[RypEvHKD8QQKFhvQ6QleEB4J58tiPdvo+rtK1I9qca6aM/wvqnLSV5zEPeusUiX5L5X/0lWfrf0QADHHhGd3QczcdCUpj911L3vg3W/sYYvuJTs3TUUkSUXxaccAS0qhxchrRYt66wiSpGLYL42aM6A8dTT+6k4aSknmPj48kzJs8qLjvd4Xgpue06DOdnLxAUHzM6+kDZ+HMZfJYuR+LtwGc2hgf5gsijff0ekUNXZiqATP7PF5mZxZ3Izoun1s4zG4LUMnvw2r+KqCKIw+3IQH03v+BCA9nMELNqbSf6tiWSrXJB3LAVGUcallcrw8V2t9EL4EhzJWrQUax5wLVMNS0+rUPA3k22Ncx4XXZS9o0MBH27Bo6BpNelZpS+/uh9KsNlY6bHCmJU9p8g7m3fVKn28H3KDYA5Pl/T8Z1ptDAVe0lXdQ2YoyyH2uyPIGHBZZIs2pDBS8R07+qN+E7Q==]]></Encrypt>
                <AgentID><![CDATA[218]]></AgentID>
                </xml>';

        $request = (new ServerRequest('POST', 'http://easywechat.com/server', [], $body))->withQueryParams([
            'msg_signature' => '477715d11cdb4164915debcba66cb864d751f3e6',
            'timestamp' => '1409659813',
            'nonce' => '1372623149'
        ]);

        $app = new Application([
            'corp_id' => 'wx5823bf96d3bd56c7',
            'secret' => '',
            'token' => 'QDG6eK',
            'aes_key' => 'jWmYm7qr5nMoAUwZRjGtBxmz3KA1tkAj3ykkR6q2B2C',
        ]);

        $app->setRequest($request);

        $response = $app->getServer()->serve();

        $this->assertSame('SUCCESS', \strval($response->getBody()));
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
