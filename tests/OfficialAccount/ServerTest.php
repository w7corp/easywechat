<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\OfficialAccount;

use EasyWeChat\Kernel\Encryptor;
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

    public function test_it_will_return_echostr_as_is_for_validation_request_in_safe_mode()
    {
        $encryptor = new Encryptor('wx5823bf96d3bd56c7', 'QDG6eK', 'jWmYm7qr5nMoAUwZRjGtBxmz3KA1tkAj3ykkR6q2B2C');
        $encrypted = $encryptor->encryptAsArray(
            plaintext: '1616140317555161061',
            nonce: '1372623149',
            timestamp: '1409659813'
        );

        $request = (new ServerRequest('GET', 'http://easywechat.com/server'))->withQueryParams([
            'echostr' => $encrypted['ciphertext'],
            'msg_signature' => $encrypted['signature'],
            'timestamp' => $encrypted['timestamp'],
            'nonce' => $encrypted['nonce'],
        ]);
        $server = new Server($request, $encryptor);

        $response = $server->serve();

        $this->assertSame($encrypted['ciphertext'], \strval($response->getBody()));
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

    public function test_it_can_decrypt_json_mode_messages()
    {
        $plaintext = json_encode([
            'ToUserName' => 'wx5823bf96d3bd56c7',
            'FromUserName' => 'mycreate',
            'CreateTime' => '1409659813',
            'MsgType' => 'text',
            'Content' => 'hello',
            'MsgId' => '4561255354251345929',
        ], JSON_UNESCAPED_UNICODE);

        $this->assertIsString($plaintext);

        $encryptor = new Encryptor('wx5823bf96d3bd56c7', 'QDG6eK', 'jWmYm7qr5nMoAUwZRjGtBxmz3KA1tkAj3ykkR6q2B2C');
        $encrypted = $encryptor->encryptAsArray(
            plaintext: $plaintext,
            nonce: '1372623149',
            timestamp: '1409659813'
        );

        $body = json_encode([
            'ToUserName' => 'wx5823bf96d3bd56c7',
            'Encrypt' => $encrypted['ciphertext'],
        ], JSON_UNESCAPED_UNICODE);

        $this->assertIsString($body);

        $request = (new ServerRequest('POST', 'http://easywechat.com/server', [], $body))->withQueryParams([
            'msg_signature' => $encrypted['signature'],
            'timestamp' => $encrypted['timestamp'],
            'nonce' => $encrypted['nonce'],
        ]);

        $server = new Server($request, $encryptor);

        $message = $server->getDecryptedMessage();

        $this->assertSame('hello', $message->Content);
        $this->assertSame('mycreate', $message->FromUserName);
    }
}
