<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Work;

use EasyWeChat\Kernel\Support\Xml;
use EasyWeChat\Kernel\Traits\DecryptXmlMessage;
use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Application;
use Nyholm\Psr7\ServerRequest;

class ServerTest extends TestCase
{
    use DecryptXmlMessage;

    public function test_it_will_handle_validation_request()
    {
        $request = (new ServerRequest('GET', 'http://easywechat.com/server'))
            ->withQueryParams([
                'msg_signature' => '5c45ff5e21c57e6ad56bac8758b79b1d9ac89fd3',
                'timestamp' => '1409659589',
                'nonce' => '263014780',
                'echostr' => 'P9nAzCzyDtyTWESHep1vC5X9xho/qYX3Zpb4yKa9SKld1DsH3Iyt3tP3zNdtp+4RPcs8TgAE7OaBO+FZXvnaqQ==',
            ]);

        $app = new Application([
            'corp_id' => 'wx5823bf96d3bd56c7',
            'secret' => 'secret',
            'token' => 'QDG6eK',
            'aes_key' => 'jWmYm7qr5nMoAUwZRjGtBxmz3KA1tkAj3ykkR6q2B2C',
        ]);
        $app->setRequest($request);

        $response = $app->getServer()->serve();

        $this->assertSame('1616140317555161061', \strval($response->getBody()));
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
            'nonce' => '1372623149',
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
                <ToUserName><![CDATA[wx5823bf96d3bd56c7]]></ToUserName>
                <Encrypt><![CDATA[RypEvHKD8QQKFhvQ6QleEB4J58tiPdvo+rtK1I9qca6aM/wvqnLSV5zEPeusUiX5L5X/0lWfrf0QADHHhGd3QczcdCUpj911L3vg3W/sYYvuJTs3TUUkSUXxaccAS0qhxchrRYt66wiSpGLYL42aM6A8dTT+6k4aSknmPj48kzJs8qLjvd4Xgpue06DOdnLxAUHzM6+kDZ+HMZfJYuR+LtwGc2hgf5gsijff0ekUNXZiqATP7PF5mZxZ3Izoun1s4zG4LUMnvw2r+KqCKIw+3IQH03v+BCA9nMELNqbSf6tiWSrXJB3LAVGUcallcrw8V2t9EL4EhzJWrQUax5wLVMNS0+rUPA3k22Ncx4XXZS9o0MBH27Bo6BpNelZpS+/uh9KsNlY6bHCmJU9p8g7m3fVKn28H3KDYA5Pl/T8Z1ptDAVe0lXdQ2YoyyH2uyPIGHBZZIs2pDBS8R07+qN+E7Q==]]></Encrypt>
                <AgentID><![CDATA[218]]></AgentID>
                </xml>';

        $request = (new ServerRequest('POST', 'http://easywechat.com/server', [], $body))->withQueryParams([
            'msg_signature' => '477715d11cdb4164915debcba66cb864d751f3e6',
            'timestamp' => '1409659813',
            'nonce' => '1372623149',
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

    public function test_it_will_respond_from_message_handlers()
    {
        $app = new Application([
            'corp_id' => 'wx5823bf96d3bd56c7',
            'secret' => '',
            'token' => 'QDG6eK',
            'aes_key' => 'jWmYm7qr5nMoAUwZRjGtBxmz3KA1tkAj3ykkR6q2B2C',
        ]);

        $request = $this->createEncryptedXmlMessageRequest('<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[sys]]></FromUserName> 
            <CreateTime>1403610513</CreateTime>
            <MsgType><![CDATA[event]]></MsgType>
            <Event><![CDATA[change_contact]]></Event>
            <ChangeType>change_contact</ChangeType>
            <UserID><![CDATA[zhangsan]]></UserID>
        </xml>', $app->getEncryptor());

        $app->setRequest($request);

        $response = $app->getServer()
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

        $message = Xml::parse(\strval($response->getBody()));

        $response = Xml::parse($app->getEncryptor()->decrypt($message['Encrypt'], $message['MsgSignature'], $message['Nonce'], $message['TimeStamp']));

        $this->assertSame('sys', $response['ToUserName']);
        $this->assertSame('toUser', $response['FromUserName']);
        $this->assertSame('text', $response['MsgType']);
        $this->assertSame('hello', $response['Content']);
    }

    public function test_it_will_respond_from_event_handlers()
    {
        $app = new Application([
            'corp_id' => 'wx5823bf96d3bd56c7',
            'secret' => '',
            'token' => 'QDG6eK',
            'aes_key' => 'jWmYm7qr5nMoAUwZRjGtBxmz3KA1tkAj3ykkR6q2B2C',
        ]);

        $request = $this->createEncryptedXmlMessageRequest('<xml>
            <ToUserName><![CDATA[toUser]]></ToUserName>
            <FromUserName><![CDATA[sys]]></FromUserName> 
            <CreateTime>1403610513</CreateTime>
            <MsgType><![CDATA[event]]></MsgType>
            <Event><![CDATA[change_contact]]></Event>
            <ChangeType>change_contact</ChangeType>
            <UserID><![CDATA[zhangsan]]></UserID>
        </xml>', $app->getEncryptor());

        $app = new Application([
            'corp_id' => 'wx5823bf96d3bd56c7',
            'secret' => '',
            'token' => 'QDG6eK',
            'aes_key' => 'jWmYm7qr5nMoAUwZRjGtBxmz3KA1tkAj3ykkR6q2B2C',
        ]);

        $app->setRequest($request);

        $response = $app->getServer()
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

        $message = Xml::parse(\strval($response->getBody()));

        $response = Xml::parse($app->getEncryptor()->decrypt($message['Encrypt'], $message['MsgSignature'], $message['Nonce'], $message['TimeStamp']));

        $this->assertSame('sys', $response['ToUserName']);
        $this->assertSame('toUser', $response['FromUserName']);
        $this->assertSame('text', $response['MsgType']);
        $this->assertSame('hello', $response['Content']);
    }
}
