<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenWork\Server;

use EasyWeChat\Kernel\Config;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OpenWork\Application;
use EasyWeChat\OpenWork\Server\Guard;
use EasyWeChat\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class GuardTest extends TestCase
{
    public function testValidate()
    {
        $app = new ServiceContainer([]);
        $guard = new Guard($app);
        $this->assertSame($guard, $guard->validate());
    }

    public function testShouldReturnRawResponse()
    {
        $request = Request::create('/path/to/server?foo=bar');
        $app = new ServiceContainer([], ['request' => $request]);
        $guard = \Mockery::mock(Guard::class, [$app])->makePartial();
        $this->assertFalse($guard->shouldReturnRawResponse());

        $request = Request::create('/path/to/server?echostr=hello');
        $app = new ServiceContainer([], ['request' => $request]);
        $guard = \Mockery::mock(Guard::class, [$app])->makePartial();
        $this->assertTrue($guard->shouldReturnRawResponse());
    }

    public function testIsSafeMode()
    {
        $app = new ServiceContainer([]);
        $guard = \Mockery::mock(Guard::class, [$app])->makePartial();
        $this->assertTrue($guard->isSafeMode());
    }

    public function testDecryptMessage()
    {
        $config = [
            'token' => 'QDG6eK',
            'aes_key' => 'jWmYm7qr5nMoAUwZRjGtBxmz3KA1tkAj3ykkR6q2B2C',
        ];

        $message = [
            'ToUserName' => 'wx5823bf96d3bd56c7',
            'Encrypt' => 'RypEvHKD8QQKFhvQ6QleEB4J58tiPdvo+rtK1I9qca6aM/wvqnLSV5zEPeusUiX5L5X/0lWfrf0QADHHhGd3QczcdCUpj911L3vg3W/sYYvuJTs3TUUkSUXxaccAS0qhxchrRYt66wiSpGLYL42aM6A8dTT+6k4aSknmPj48kzJs8qLjvd4Xgpue06DOdnLxAUHzM6+kDZ+HMZfJYuR+LtwGc2hgf5gsijff0ekUNXZiqATP7PF5mZxZ3Izoun1s4zG4LUMnvw2r+KqCKIw+3IQH03v+BCA9nMELNqbSf6tiWSrXJB3LAVGUcallcrw8V2t9EL4EhzJWrQUax5wLVMNS0+rUPA3k22Ncx4XXZS9o0MBH27Bo6BpNelZpS+/uh9KsNlY6bHCmJU9p8g7m3fVKn28H3KDYA5Pl/T8Z1ptDAVe0lXdQ2YoyyH2uyPIGHBZZIs2pDBS8R07+qN+E7Q==',
        ];

        $msgSignature = '477715d11cdb4164915debcba66cb864d751f3e6';
        $nonce = '1372623149';
        $timeStamp = '1409659813';

        $request = Request::create(sprintf('foo/bar/server?msg_signature=%s&nonce=%s&timestamp=%s', $msgSignature, $nonce, $timeStamp));
        $app = new Application([$config], [
            'request' => $request,
            'config' => new Config($config),
        ]);

        $encryptor = new Encryptor($message['ToUserName'], $config['token'], $config['aes_key']);

        $decryptMessage = $encryptor->decrypt($message['Encrypt'], $msgSignature, $nonce, $timeStamp);

        $guard = \Mockery::mock(Guard::class, [$app])->makePartial()->shouldAllowMockingProtectedMethods();

        $this->assertSame($decryptMessage, $guard->decryptMessage($message));
    }
}
