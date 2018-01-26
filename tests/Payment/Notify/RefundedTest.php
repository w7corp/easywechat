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
use EasyWeChat\Payment\Notify\Refunded;
use EasyWeChat\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RefundedTest extends TestCase
{
    private function makeApp($config = [])
    {
        return new Application(array_merge([
            'key' => 'foo-merchant-key',
        ], $config));
    }

    public function testRefundedNotify()
    {
        $app = $this->makeApp();
        $app['request'] = Request::create('', 'POST', [], [], [], [], '<xml>
<foo>bar</foo>
<sign>280F9CB28E99DC917792FCC7AC1B88C4</sign>
</xml>');

        $notify = new Refunded($app);

        $that = $this;
        $response = $notify->handle(function ($message) use ($that) {
            $that->assertSame([
                'foo' => 'bar',
                'sign' => '280F9CB28E99DC917792FCC7AC1B88C4',
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

        $response = $notify->handle(function ($msg, $reqInfo, $fail) {
            $fail('fails.');
        });

        $this->assertSame([
            'return_code' => 'FAIL',
            'return_msg' => 'fails.',
        ], XML::parse($response->getContent()));
    }

    public function testReqInfo()
    {
        $app = $this->makeApp();
        $app['request'] = Request::create('', 'POST', [], [], [], [], '<xml>
<req_info>FTuPWOW4npLOTY8GQp+JtWXKe3ZFa2wsJC6v9gFoATU=</req_info>
<sign>280F9CB28E99DC917792FCC7AC1B88C4</sign>
</xml>');

        $notify = new Refunded($app);

        $this->assertSame(['bar' => '123'], $notify->reqInfo());
    }

    public function testDecryptMessage()
    {
        $app = $this->makeApp();
        $app['request'] = Request::create('', 'POST', [], [], [], [], '<xml>
<req_info>FTuPWOW4npLOTY8GQp+JtWXKe3ZFa2wsJC6v9gFoATU=</req_info>
<sign>280F9CB28E99DC917792FCC7AC1B88C4</sign>
</xml>');
        $notify = new Refunded($app);
        $this->assertSame('<foo><bar>123</bar></foo>', $notify->decryptMessage('req_info'));
        $this->assertNull($notify->decryptMessage('not-exists'));
    }

    public function testGetMessage()
    {
        $app = $this->makeApp();
        $app['request'] = Request::create('', 'POST', [], [], [], [], 'invalid-xml');
        $notify = new Refunded($app);
        $this->expectException('\EasyWeChat\Kernel\Exceptions\Exception');
        $notify->getMessage();
    }

    public function testGetMessageButMessageIsEmpty()
    {
        $app = $this->makeApp();
        $app['request'] = Request::create('', 'POST', [], [], [], [], '<xml></xml>');
        $notify = new Refunded($app);
        $this->expectException('\EasyWeChat\Kernel\Exceptions\Exception');
        $notify->getMessage();
    }
}
