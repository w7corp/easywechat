<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Payment\Notify;

use EasyWeChat\Kernel\Support\XML;
use EasyWeChat\Payment\Application;
use EasyWeChat\Payment\Kernel\Exceptions\InvalidSignException;
use EasyWeChat\Payment\Notify\Paid;
use EasyWeChat\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PaidTest extends TestCase
{
    private function makeApp($config = [])
    {
        return new Application(array_merge([
            'key' => '88888888888888888888888888888888',
        ], $config));
    }

    public function testPaidNotify()
    {
        $app = $this->makeApp();
        $app['request'] = Request::create('', 'POST', [], [], [], [], '<xml>
<foo>bar</foo>
<sign>834A25C9A5B48305AB997C9A7E101530</sign>
</xml>');

        $notify = new Paid($app);

        $that = $this;
        $response = $notify->handle(function ($message) use ($that) {
            $that->assertSame([
                'foo' => 'bar',
                'sign' => '834A25C9A5B48305AB997C9A7E101530',
            ], $message);

            return true;
        });

        // return true.
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame([
            'return_code' => 'SUCCESS',
            'return_msg' => null,
        ], XML::parse($response->getContent()));

        // return false.
        $response = $notify->handle(function () {
            return false;
        });

        $this->assertSame([
            'return_code' => 'FAIL',
            'return_msg' => null,
        ], XML::parse($response->getContent()));

        // empty return.
        $response = $notify->handle(function () {
        });

        $this->assertSame([
            'return_code' => 'FAIL',
            'return_msg' => null,
        ], XML::parse($response->getContent()));

        $response = $notify->handle(function ($msg, $fail) {
            $fail('fails.');
        });

        $this->assertSame([
            'return_code' => 'FAIL',
            'return_msg' => 'fails.',
        ], XML::parse($response->getContent()));
    }

    public function testInvalidSign()
    {
        $app = $this->makeApp();
        $app['request'] = Request::create('', 'POST', [], [], [], [], '<xml>
<foo>bar</foo>
<sign>invalid-sign</sign>
</xml>');
        $notify = new Paid($app);
        $this->expectException(InvalidSignException::class);
        $notify->handle(function () {
        });
    }
}
