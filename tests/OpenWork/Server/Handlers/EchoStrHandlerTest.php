<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenWork\Server\Handlers;

use EasyWeChat\Kernel\Decorators\FinallyResult;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\OpenWork\Application;
use EasyWeChat\OpenWork\Server\Handlers\EchoStrHandler;
use EasyWeChat\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class EchoStrHandlerTest extends TestCase
{
    public function testHandleWithEchostr()
    {
        $config = [
            'token' => 'mock-token',
            'aes_key' => 'mock-aes-key',
        ];
        $msgSignature = 'mock-signature';
        $nonce = 'mock-nonce';
        $timeStamp = 'mock-timestamp';

        $request = Request::create(sprintf('foo/bar/server?msg_signature=%s&nonce=%s&timestamp=%s&echostr=mock-echo-str', $msgSignature, $nonce, $timeStamp));

        $encryptor_corp = \Mockery::mock(Encryptor::class);
        $encryptor_corp->expects()->decrypt('mock-echo-str', 'mock-signature', 'mock-nonce', 'mock-timestamp')->andReturn('decrypted');
        $app = new Application([$config], [
            'request' => $request,
            'encryptor_corp' => $encryptor_corp,
        ]);

        $handler = \Mockery::mock(EchoStrHandler::class, [$app])->makePartial();
        $this->assertInstanceOf(FinallyResult::class, $handler->handle());
    }

    public function testHandleWithSuiteTicket()
    {
        $request = Request::create('foo/bar/server');
        $app = new Application([], [
            'request' => $request,
        ]);

        $handler = \Mockery::mock(EchoStrHandler::class, [$app])->makePartial()->shouldAllowMockingProtectedMethods();
        $handler->handle([
            'SuiteTicket' => 'mock-suite-ticket',
        ]);

        $this->assertSame('mock-suite-ticket', $app['suite_ticket']->getTicket());
    }
}
