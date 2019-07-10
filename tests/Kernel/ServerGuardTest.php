<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Kernel\Server;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Raw;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\ServerGuard;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\Kernel\Support\XML;
use EasyWeChat\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ServerGuardTest extends TestCase
{
    public function testServe()
    {
        $response = new Response('success');
        $logger = \Mockery::mock('stdClass');
        $logger->expects()->debug('Server response created:', ['content' => 'success']);
        $logger->expects()->debug('Request received:', [
            'method' => 'POST',
            'uri' => 'http://localhost/path/to/resource?foo=bar',
            'content-type' => 'xml',
            'content' => '<xml><name>foo</name></xml>',
        ]);

        $request = Request::create('/path/to/resource?foo=bar', 'POST', ['foo' => 'bar'], [], [], [
            'CONTENT_TYPE' => ['application/xml'],
        ], '<xml><name>foo</name></xml>');

        $app = new ServiceContainer(['token' => 'mock-token'], [
            'logger' => $logger,
            'request' => $request,
        ]);

        $guard = \Mockery::mock(ServerGuard::class.'[validate,resolve]', [$app])->shouldAllowMockingProtectedMethods();
        $guard->expects()->validate()->andReturnSelf();
        $guard->expects()->resolve()->andReturn($response);

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
            'encrypt_type' => 'aes',
        ], [], [], [
            'CONTENT_TYPE' => ['application/xml'],
        ], '<xml><name>foo</name></xml>');

        $app = new ServiceContainer([
            'token' => 'mock-token',
        ], [
            'request' => $request,
        ]);
        $guard = new ServerGuard($app);
        $this->assertSame($guard, $guard->validate());
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
            'encrypt_type' => 'aes',
            'signature' => $signature.'xxxx', // invalid signature
        ], [], [], [
            'CONTENT_TYPE' => ['application/xml'],
        ], '<xml><name>foo</name></xml>');

        $app = new ServiceContainer([], [
            'request' => $request,
        ]);
        $guard = new ServerGuard($app);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Invalid request signature.');
        $this->expectExceptionCode(400);
        $guard->validate();
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
        $guard = new ServerGuard($app);
        $this->assertSame($guard, $guard->validate());
    }

    public function testGetMessage()
    {
        $request = Request::create('/path/to/resource?foo=bar', 'POST', [], [], [], [
            'CONTENT_TYPE' => ['application/xml'],
        ], '<xml><Content>foo</Content><MsgType>text</MsgType></xml>');

        $app = new ServiceContainer([], [
            'request' => $request,
        ]);
        $guard = new ServerGuard($app);
        $this->assertSame(['Content' => 'foo', 'MsgType' => 'text'], $guard->getMessage());
    }

    public function testGetMessageInSafeMode()
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
        $encryptor = \Mockery::mock(Encryptor::class);

        // xml
        $request = Request::create('/path/to/resource?foo=bar', 'POST', [
            'nonce' => $nonce,
            'timestamp' => $time,
            'signature' => $signature,
            'msg_signature' => 'mock-msg-signature',
            'encrypt_type' => 'aes',
        ], [], [], [
            'CONTENT_TYPE' => ['application/xml'],
        ], '<xml><Encrypt>encrypted content</Encrypt></xml>');
        $encryptor->allows()->decrypt('encrypted content', 'mock-msg-signature', $nonce, $time)
            ->andReturn(XML::build(['foo' => 'bar']));

        $app = new ServiceContainer([], [
            'request' => $request,
            'encryptor' => $encryptor,
        ]);
        $guard = new ServerGuard($app);
        $this->assertSame(['foo' => 'bar'], $guard->getMessage());

        // json
        $request = Request::create('/path/to/resource?foo=bar', 'POST', [
            'nonce' => $nonce,
            'timestamp' => $time,
            'signature' => $signature,
            'msg_signature' => 'mock-msg-signature2',
            'encrypt_type' => 'aes',
        ], [], [], [
            'CONTENT_TYPE' => ['application/json'],
        ], '<xml><Encrypt>encrypted content</Encrypt></xml>');
        $encryptor->allows()->decrypt('encrypted content', 'mock-msg-signature2', $nonce, $time)
            ->andReturn(json_encode(['foo' => 'bar']));

        $app = new ServiceContainer([], [
            'request' => $request,
            'encryptor' => $encryptor,
        ]);
        $guard = new ServerGuard($app);
        $this->assertSame(['foo' => 'bar'], $guard->getMessage());
    }

    public function testGetMessageWithInvalidContent()
    {
        $request = Request::create('/path/to/resource?foo=bar', 'POST', [], [], [], [
            'CONTENT_TYPE' => ['application/xml'],
        ], 'not-xml-content');

        $app = new ServiceContainer([], [
            'request' => $request,
        ]);
        $guard = new ServerGuard($app);

        $this->assertSame(['not-xml-content'], $guard->getMessage());
    }

    public function testGetMessageWithEmptyContent()
    {
        $request = Request::create('/path/to/resource?foo=bar', 'POST', [], [], [], [
            'CONTENT_TYPE' => ['application/xml'],
        ], '<xml></xml>');

        $app = new ServiceContainer([], [
            'request' => $request,
        ]);
        $guard = new ServerGuard($app);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('No message received.');

        $guard->getMessage();
    }

    public function testResolve()
    {
        $request = Request::create('/path/to/resource', 'POST', [], [], [], [
        'CONTENT_TYPE' => ['application/xml'],
    ], '<xml><foo>bar</foo></xml>');

        $app = new ServiceContainer([], [
            'request' => $request,
        ]);
        $guard = \Mockery::mock(ServerGuard::class.'[handleRequest,shouldReturnRawResponse,buildResponse]', [$app])->shouldAllowMockingProtectedMethods()->makePartial();

        $guard->allows()->handleRequest()->andReturn([
            'to' => 'overtrue',
            'from' => 'easywechat',
            'response' => 'hello overtrue!',
        ]);
        $guard->expects()->buildResponse('overtrue', 'easywechat', 'hello overtrue!')->andReturn('success')->twice();
        $this->assertInstanceOf(Response::class, $guard->resolve());
        $this->assertSame('success', $guard->resolve()->getContent());
    }

    public function testResolveWithRawResponse()
    {
        $request = Request::create('/path/to/resource', 'POST', [], [], [], [
            'CONTENT_TYPE' => ['application/xml'],
        ], '<xml><foo>bar</foo></xml>');

        $app = new ServiceContainer([], [
            'request' => $request,
        ]);
        $guard = \Mockery::mock(ServerGuard::class.'[handleRequest,shouldReturnRawResponse,buildResponse]', [$app])->shouldAllowMockingProtectedMethods()->makePartial();

        // return raw
        $guard->allows()->handleRequest()->andReturn([
            'to' => 'overtrue',
            'from' => 'easywechat',
            'response' => 'hello overtrue!',
        ]);
        $guard->expects()->shouldReturnRawResponse()->andReturn(true);
        $this->assertSame('hello overtrue!', $guard->resolve()->getContent());
    }

    public function testBuildResponse()
    {
        $guard = new ServerGuard(new ServiceContainer());

        // empty
        $this->assertSame(ServerGuard::SUCCESS_EMPTY_RESPONSE, $guard->buildResponse('overtrue', 'easywechat', ''));
        $this->assertSame(ServerGuard::SUCCESS_EMPTY_RESPONSE, $guard->buildResponse('overtrue', 'easywechat', null));
        $this->assertSame(ServerGuard::SUCCESS_EMPTY_RESPONSE, $guard->buildResponse('overtrue', 'easywechat', 0));
        $this->assertSame(ServerGuard::SUCCESS_EMPTY_RESPONSE, $guard->buildResponse('overtrue', 'easywechat', []));

        // 'success'
        $this->assertSame(ServerGuard::SUCCESS_EMPTY_RESPONSE, $guard->buildResponse('overtrue', 'easywechat', ServerGuard::SUCCESS_EMPTY_RESPONSE));

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
        $this->assertSame('welcome to easywechat.com', $response['Content']);

        $this->assertSame('overtrue', $response['ToUserName']);
        $this->assertSame('easywechat', $response['FromUserName']);
        $this->assertSame('welcome to easywechat.com', $response['Content']);

        // news
        $item1 = new NewsItem([
            'title' => 'mock-title-1',
            'description' => 'mock-description-1',
            'url' => 'mock-url-1',
            'image' => 'mock-image-1',
        ]);
        $item2 = new NewsItem([
            'title' => 'mock-title-2',
            'description' => 'mock-description-2',
            'url' => 'mock-url-2',
            'image' => 'mock-image-2',
        ]);
        $response = XML::parse($guard->buildResponse('overtrue', 'easywechat', [$item1, $item2]));
        $this->assertArrayHasKey('ToUserName', $response);
        $this->assertArrayHasKey('FromUserName', $response);
        $this->assertArrayHasKey('CreateTime', $response);
        $this->assertArrayHasKey('MsgType', $response);
        $this->assertArrayHasKey('ArticleCount', $response);
        $this->assertArrayHasKey('Articles', $response);
        $this->assertSame('2', $response['ArticleCount']);
        $this->assertCount(2, $response['Articles']['item']);
        $this->assertArraySubset($item1->toXmlArray(), $response['Articles']['item'][0]);
        $this->assertArraySubset($item2->toXmlArray(), $response['Articles']['item'][1]);
        $this->assertSame('overtrue', $response['ToUserName']);
        $this->assertSame('easywechat', $response['FromUserName']);

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
        $params = [
            'mock-token',
            $time,
            $nonce,
        ];
        sort($params, SORT_STRING);
        $signature = sha1(implode($params));
        $logger = \Mockery::mock('stdClass');
        $logger->expects()->debug('Messages safe mode is enabled.');
        $request = Request::create('/path/to/resource?foo=bar', 'POST', [
            'timestamp' => $time,
            'nonce' => $nonce,
            'signature' => $signature,
            'encrypt_type' => 'aes',
        ], [], [], [
            'CONTENT_TYPE' => ['application/xml'],
        ], '<xml><Content>foo</Content><MsgType>text</MsgType></xml>');
        $encryptor = \Mockery::mock(Encryptor::class);
        $encryptor->allows()->encrypt(\Mockery::on(function ($xml) {
            $array = XML::parse($xml);
            $this->assertSame('overtrue', $array['ToUserName']);
            $this->assertSame('easywechat', $array['FromUserName']);
            $this->assertSame('text', $array['MsgType']);
            $this->assertTrue($array['CreateTime'] <= time());
            $this->assertSame('hello world!', $array['Content']);

            return true;
        }))->andReturn('mock-encrypted-response');
        $app = new ServiceContainer([], [
            'logger' => $logger,
            'request' => $request,
            'encryptor' => $encryptor,
        ]);

        $guard = new ServerGuard($app);
        $this->assertSame('mock-encrypted-response', $guard->buildResponse('overtrue', 'easywechat', 'hello world!'));
    }

    public function testHandleRequest()
    {
        $guard = \Mockery::mock(ServerGuard::class, [new ServiceContainer()])->makePartial();

        // no message type
        $message = [
            'FromUserName' => 'overtrue',
            'ToUserName' => 'easywechat',
        ];
        $guard->expects()->getMessage()->andReturn($message);
        $guard->expects()->dispatch(ServerGuard::MESSAGE_TYPE_MAPPING['text'], $message)->andReturn('mock-response');
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
        $guard->expects()->getMessage()->andReturn($message);
        $guard->expects()->dispatch(ServerGuard::MESSAGE_TYPE_MAPPING['image'], $message)->andReturn('mock-response');
        $this->assertSame([
            'to' => 'overtrue',
            'from' => 'easywechat',
            'response' => 'mock-response',
        ], $guard->handleRequest());

        // object message type
        $message = new \stdClass();
        $message->FromUserName = 'overtrue';
        $message->ToUserName = 'easywechat';
        $message->MsgType = 'file';

        $guard->expects()->getMessage()->andReturn($message);
        $guard->expects()->dispatch(ServerGuard::MESSAGE_TYPE_MAPPING['file'], $message)->andReturn('mock-response');
        $this->assertSame([
            'to' => 'overtrue',
            'from' => 'easywechat',
            'response' => 'mock-response',
        ], $guard->handleRequest());
    }

    public function testParseMessageWithSafeMode()
    {
        $timestamp = time();
        $xml = (new Text('hello world!'))->transformToXml([
            'ToUserName' => 'overtrue',
            'FromUserName' => 'easywechat',
            'MsgType' => 'text',
            'CreateTime' => $timestamp,
        ]);

        $request = Request::create('/path/to/resource?foo=bar', 'POST', []);

        $app = new ServiceContainer([
            'app_id' => 'appId',
            'token' => 'mock-token',
        ], [
            'request' => $request,
        ]);

        $guard = \Mockery::mock(ServerGuard::class, [$app])->makePartial();

        $this->assertSame([
            'MsgType' => 'text',
            'Content' => 'hello world!',
            'ToUserName' => 'overtrue',
            'FromUserName' => 'easywechat',
            'CreateTime' => strval($timestamp),
        ], $guard->parseMessage($xml));

        // json format
        $this->assertSame([
            'MsgType' => 'text',
            'Content' => 'hello world!',
            'ToUserName' => 'overtrue',
            'FromUserName' => 'easywechat',
            'CreateTime' => strval($timestamp),
        ], $guard->parseMessage(json_encode(XML::parse($xml))));
    }

    public function testParseMessageWithInvalidContent()
    {
        $request = Request::create('/path/to/resource?foo=bar', 'POST', []);
        $app = new ServiceContainer([
            'app_id' => 'appId',
            'token' => 'mock-token',
        ], [
            'request' => $request,
        ]);

        $guard = \Mockery::mock(ServerGuard::class, [$app])->makePartial();

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Invalid message content:(2) simplexml_load_string(): Entity: line 1: parser error : Couldn\'t find end of Start Tag hello line 1');

        $guard->parseMessage('<hello');
    }

    public function testIsSafeMode()
    {
        // signature & encrypt_type
        $request = Request::create('/path/to/resource?foo=bar&signature=xxx&encrypt_type=aes', 'POST', []);
        $app = new ServiceContainer([
            'app_id' => 'appId',
            'token' => 'mock-token',
        ], [
            'request' => $request,
        ]);
        $guard = \Mockery::mock(DummyClassForServerGuardTest::class, [$app])->makePartial();

        $this->assertTrue($guard->isSafeMode());

        // signature
        $request = Request::create('/path/to/resource?foo=bar&signature=xxx', 'POST', []);
        $app = new ServiceContainer([
            'app_id' => 'appId',
            'token' => 'mock-token',
        ], [
            'request' => $request,
        ]);
        $guard = \Mockery::mock(DummyClassForServerGuardTest::class, [$app])->makePartial();

        $this->assertFalse($guard->isSafeMode());

        // encrypt_type
        $request = Request::create('/path/to/resource?foo=bar&encrypt_type=aes', 'POST', []);
        $app = new ServiceContainer([
            'app_id' => 'appId',
            'token' => 'mock-token',
        ], [
            'request' => $request,
        ]);
        $guard = \Mockery::mock(DummyClassForServerGuardTest::class, [$app])->makePartial();

        $this->assertFalse($guard->isSafeMode());
    }

    public function testForceValidate()
    {
        $app = new ServiceContainer();
        $guard = \Mockery::mock(ServerGuard::class, [$app])->makePartial();
        $reflectionProperty = new \ReflectionProperty($guard, 'alwaysValidate');
        $reflectionProperty->setAccessible(true);

        $this->assertFalse($reflectionProperty->getValue($guard));

        $guard->forceValidate();

        $this->assertTrue($reflectionProperty->getValue($guard));
    }
}

class DummyClassForServerGuardTest extends ServerGuard
{
}
