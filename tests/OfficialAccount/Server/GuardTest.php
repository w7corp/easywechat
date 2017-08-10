<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Server;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\Raw;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\Kernel\Support\XML;
use EasyWeChat\OfficialAccount\Server\Guard;
use EasyWeChat\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GuardTest extends TestCase
{
    public function testServe()
    {
        $response = new Response('success');
        $logger = \Mockery::mock('stdClass');
        $logger->expects()->debug('Server response created:', ['content' => 'success'])->once();
        $logger->expects()->debug('Request received:', [
            'method' => 'POST',
            'uri' => 'http://localhost/path/to/resource?foo=bar',
            'content-type' => 'xml',
            'content' => '<xml><name>foo</name></xml>',
        ])->once();

        $request = Request::create('/path/to/resource?foo=bar', 'POST', ['foo' => 'bar'], [], [], [
            'CONTENT_TYPE' => ['application/xml'],
        ], '<xml><name>foo</name></xml>');

        $app = new ServiceContainer(['token' => 'mock-token'], [
            'logger' => $logger,
            'request' => $request,
        ]);

        $guard = \Mockery::mock(Guard::class.'[validate,resolve]', [$app])->shouldAllowMockingProtectedMethods();
        $guard->expects()->validate('mock-token')->andReturnSelf()->once();
        $guard->expects()->resolve()->andReturn($response)->once();

        $this->assertSame($response, $guard->serve());
    }

    public function testValidate()
    {
        $time = time();
        $nonce = 'foobar';
        $params = [
            'mock-token',
            $time,
            $nonce,
        ];
        sort($params, SORT_STRING);
        $signature = sha1(implode($params));

        // with signature
        $request = Request::create('/path/to/resource?foo=bar', 'POST', [
            'timestamp' => $time,
            'nonce' => $nonce,
            'signature' => $signature,
        ], [], [], [
            'CONTENT_TYPE' => ['application/xml'],
        ], '<xml><name>foo</name></xml>');

        $app = new ServiceContainer([], [
            'request' => $request,
        ]);
        $guard = new Guard($app);
        $this->assertSame($guard, $guard->validate('mock-token'));
    }

    public function testValidateWithInvalidSignature()
    {
        $time = time();
        $nonce = 'foobar';
        $params = [
            'mock-token',
            $time,
            $nonce,
        ];
        sort($params, SORT_STRING);
        $signature = sha1(implode($params));

        // with signature
        $request = Request::create('/path/to/resource?foo=bar', 'POST', [
            'timestamp' => $time,
            'nonce' => $nonce,
            'signature' => $signature.'xxxx', // invalid signature
        ], [], [], [
            'CONTENT_TYPE' => ['application/xml'],
        ], '<xml><name>foo</name></xml>');

        $app = new ServiceContainer([], [
            'request' => $request,
        ]);
        $guard = new Guard($app);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Invalid request signature.');
        $this->expectExceptionCode(400);
        $guard->validate('mock-token');
    }

    public function testValidateWithoutSignature()
    {
        $time = time();
        $nonce = 'foobar';

        // without signature
        $request = Request::create('/path/to/resource?foo=bar', 'POST', [
            'timestamp' => $time,
            'nonce' => $nonce,
        ], [], [], [
            'CONTENT_TYPE' => ['application/xml'],
        ], '<xml><name>foo</name></xml>');

        $app = new ServiceContainer([], [
            'request' => $request,
        ]);
        $guard = new Guard($app);
        $this->assertSame($guard, $guard->validate('mock-token'));
    }

    public function testGetMessage()
    {
        $request = Request::create('/path/to/resource?foo=bar', 'POST', [], [], [], [
            'CONTENT_TYPE' => ['application/xml'],
        ], '<xml><name>foo</name></xml>');

        $app = new ServiceContainer([], [
            'request' => $request,
        ]);
        $guard = new Guard($app);
        $this->assertSame(['name' => 'foo'], $guard->getMessage());
    }

    public function testGetMessageWithInvalidContent()
    {
        $request = Request::create('/path/to/resource?foo=bar', 'POST', [], [], [], [
            'CONTENT_TYPE' => ['application/xml'],
        ], 'not-xml-content');

        $app = new ServiceContainer([], [
            'request' => $request,
        ]);
        $guard = new Guard($app);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Invalid message content:(2) simplexml_load_string(): Entity: line 1: parser error : Start tag expected, \'<\' not found');

        $guard->getMessage();
    }

    public function testGetMessageWithEmptyContent()
    {
        $request = Request::create('/path/to/resource?foo=bar', 'POST', [], [], [], [
            'CONTENT_TYPE' => ['application/xml'],
        ], '<xml></xml>');

        $app = new ServiceContainer([], [
            'request' => $request,
        ]);
        $guard = new Guard($app);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('No message received.');

        $guard->getMessage();
    }

    public function testResolveWithEchoStr()
    {
        $logger = \Mockery::mock('stdClass');
        $logger->expects()->debug("Output 'echostr' is 'foo'.")->twice();
        $request = Request::create('/path/to/resource?echostr=foo');

        $app = new ServiceContainer([], [
            'request' => $request,
            'logger' => $logger,
        ]);
        $guard = \Mockery::mock(Guard::class, [$app])->shouldAllowMockingProtectedMethods()->makePartial();

        $this->assertSame($guard, $guard->validate(''));

        $this->assertInstanceOf(Response::class, $guard->resolve());
        $this->assertSame('foo', $guard->resolve()->getContent());
    }

    public function testResolve()
    {
        $request = Request::create('/path/to/resource', 'POST', [], [], [], [
        'CONTENT_TYPE' => ['application/xml'],
    ], '<xml><foo>bar</foo></xml>');

        $app = new ServiceContainer([], [
            'request' => $request,
        ]);
        $guard = \Mockery::mock(Guard::class.'[handleRequest,buildResponse]', [$app])->shouldAllowMockingProtectedMethods()->makePartial();

        $guard->expects()->handleRequest()->andReturn([
            'to' => 'overtrue',
            'from' => 'easywechat',
            'response' => 'hello overtrue!',
        ])->twice();
        $guard->expects()->buildResponse('overtrue', 'easywechat', 'hello overtrue!')->andReturn('success')->twice();
        $this->assertInstanceOf(Response::class, $guard->resolve());
        $this->assertSame('success', $guard->resolve()->getContent());
    }

    public function testBuildResponse()
    {
        $guard = new Guard(new ServiceContainer());

        // empty
        $this->assertSame(Guard::SUCCESS_EMPTY_RESPONSE, $guard->buildResponse('overtrue', 'easywechat', ''));
        $this->assertSame(Guard::SUCCESS_EMPTY_RESPONSE, $guard->buildResponse('overtrue', 'easywechat', null));
        $this->assertSame(Guard::SUCCESS_EMPTY_RESPONSE, $guard->buildResponse('overtrue', 'easywechat', 0));
        $this->assertSame(Guard::SUCCESS_EMPTY_RESPONSE, $guard->buildResponse('overtrue', 'easywechat', []));

        // 'success'
        $this->assertSame(Guard::SUCCESS_EMPTY_RESPONSE, $guard->buildResponse('overtrue', 'easywechat', Guard::SUCCESS_EMPTY_RESPONSE));

        // raw message
        $message = new Raw('<xml><foo>bar</foo></xml>');
        $this->assertSame($message->content, $guard->buildResponse('overtrue', 'easywechat', $message));

        // string | numeric
        $response = XML::parse($guard->buildResponse('overtrue', 'easywechat', 'welcome to easywechat.com'));
        $this->assertArrayHasKey('ToUserName', $response);
        $this->assertArrayHasKey('FromUserName', $response);
        $this->assertArrayHasKey('CreateTime', $response);
        $this->assertArrayHasKey('MsgType', $response);
        $this->assertArrayHasKey('Content', $response);

        $this->assertSame('overtrue', $response['ToUserName']);
        $this->assertSame('easywechat', $response['FromUserName']);
        $this->assertSame('welcome to easywechat.com', $response['Content']);

        // not message
        try {
            $guard->buildResponse('overtrue', 'easywechat', new \stdClass());
        } catch (\Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame('Invalid Messages type "object".', $e->getMessage());
        }

        // safe mode
        $time = time();
        $nonce = 'foobar';
        $logger = \Mockery::mock('stdClass');
        $logger->expects()->debug('Messages safe mode is enabled.')->once();
        $request = Request::create('/path/to/resource?foo=bar', 'POST', [
            'timestamp' => $time,
            'nonce' => $nonce,
            'encrypt_type' => 'aes',
        ], [], [], [
            'CONTENT_TYPE' => ['application/xml'],
        ], '<xml><name>foo</name></xml>');
        $encryptor = \Mockery::mock(Encryptor::class);
        $encryptor->allows()->encrypt(\Mockery::on(function ($xml) {
            $array = XML::parse($xml);
            $this->assertSame('overtrue', $array['ToUserName']);
            $this->assertSame('easywechat', $array['FromUserName']);
            $this->assertSame('text', $array['MsgType']);
            $this->assertTrue($array['CreateTime'] >= time());
            $this->assertSame('hello world!', $array['Content']);

            return true;
        }), $nonce, $time)->andReturn('mock-encrypted-response');
        $app = new ServiceContainer([], [
            'logger' => $logger,
            'request' => $request,
            'encryptor' => $encryptor,
        ]);

        $guard = new Guard($app);
        $this->assertSame('mock-encrypted-response', $guard->buildResponse('overtrue', 'easywechat', 'hello world!'));
    }

    public function testIsMessage()
    {
        $guard = \Mockery::mock(Guard::class, [new ServiceContainer()])->makePartial();

        $this->assertFalse($guard->isMessage(new \stdClass()));
        $this->assertTrue($guard->isMessage(new Text('hello')));

        $this->assertTrue($guard->isMessage([new Text('hello'), new Image('media-id')]));

        $this->assertFalse($guard->isMessage([new Text('hello'), new \stdClass()]));
    }

    public function testHandleRequest()
    {
        $guard = \Mockery::mock(Guard::class, [new ServiceContainer()])->makePartial();

        // no message type
        $message = [
            'FromUserName' => 'overtrue',
            'ToUserName' => 'easywechat',
        ];
        $guard->expects()->getMessage()->andReturn($message)->once();
        $guard->expects()->dispatch(Guard::MESSAGE_TYPE_MAPPING['text'], $message)->andReturn('mock-response')->once();
        $this->assertSame([
            'to' => 'overtrue',
            'from' => 'easywechat',
            'response' => 'mock-response',
        ], $guard->handleRequest());

        // with message type
        $message = [
            'FromUserName' => 'overtrue',
            'ToUserName' => 'easywechat',
            'MsgType' => 'image',
        ];
        $guard->expects()->getMessage()->andReturn($message)->once();
        $guard->expects()->dispatch(Guard::MESSAGE_TYPE_MAPPING['image'], $message)->andReturn('mock-response')->once();
        $this->assertSame([
            'to' => 'overtrue',
            'from' => 'easywechat',
            'response' => 'mock-response',
        ], $guard->handleRequest());
    }

    public function testParseMessageFromRequestWithSafeMode()
    {
        $timestamp = time();
        $nonce = 'foobar';
        $encryptor = new Encryptor('appId', 'mock-token', 'ilrEHky7P9VoudWoQpGKv4nDaWaXTv6N60Yy8oQYxXL');
        $xml = (new Text('hello world!'))->transformToXml([
            'ToUserName' => 'overtrue',
            'FromUserName' => 'easywechat',
            'MsgType' => 'text',
            'CreateTime' => $timestamp,
        ]);
        $decrypted = $encryptor->encrypt($xml, $nonce, $timestamp);

        $xmlParsed = XML::parse($decrypted);

        $request = Request::create('/path/to/resource?foo=bar', 'POST', [
            'timestamp' => $timestamp,
            'nonce' => $nonce,
            'encrypt_type' => 'aes',
            'msg_signature' => $xmlParsed['MsgSignature'],
        ]);

        $app = new ServiceContainer([
            'app_id' => 'appId',
            'token' => 'mock-token',
        ], [
            'request' => $request,
            'encryptor' => $encryptor,
        ]);

        $guard = \Mockery::mock(Guard::class, [$app])->makePartial();

        $this->assertSame([
            'MsgType' => 'text',
            'ToUserName' => 'overtrue',
            'FromUserName' => 'easywechat',
            'CreateTime' => strval($timestamp),
            'Content' => 'hello world!',
        ], $guard->parseMessageFromRequest($decrypted));

        // json format
        $this->assertSame([
            'MsgType' => 'text',
            'ToUserName' => 'overtrue',
            'FromUserName' => 'easywechat',
            'CreateTime' => strval($timestamp),
            'Content' => 'hello world!',
        ], $guard->parseMessageFromRequest(json_encode($xmlParsed)));
    }
}
