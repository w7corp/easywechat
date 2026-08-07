<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\MiniApp;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\MiniApp\Server;
use EasyWeChat\Tests\TestCase;
use Nyholm\Psr7\ServerRequest;

class ServerTest extends TestCase
{
    public const TOKEN = 'test-token';

    protected function plainMessageXml(): string
    {
        return '<xml>
          <ToUserName><![CDATA[toUser]]></ToUserName>
          <FromUserName><![CDATA[fromUser]]></FromUserName>
          <CreateTime>1348831860</CreateTime>
          <MsgType><![CDATA[text]]></MsgType>
          <Content><![CDATA[this is a test]]></Content>
          <MsgId>1234567890123456</MsgId>
        </xml>';
    }

    public function test_it_will_reject_forged_plaintext_message_when_encryptor_presents()
    {
        $encryptor = new Encryptor('wx5823bf96d3bd56c7', self::TOKEN, 'jWmYm7qr5nMoAUwZRjGtBxmz3KA1tkAj3ykkR6q2B2C');
        $request = new ServerRequest('POST', 'http://easywechat.com/server', [], $this->plainMessageXml());
        $server = new Server($request, $encryptor, self::TOKEN);

        $this->expectException(BadRequestException::class);

        $server->serve();
    }

    public function test_it_will_handle_signed_plaintext_message()
    {
        $request = $this->createPlainXmlMessageRequest($this->plainMessageXml(), self::TOKEN);
        $server = new Server($request, token: self::TOKEN);

        $this->assertSame('success', \strval($server->serve()->getBody()));
    }
}
