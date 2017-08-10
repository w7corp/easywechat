<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Payment;

use EasyWeChat\Kernel\Support\Collection;
use EasyWeChat\Kernel\Support\XML;
use EasyWeChat\Payment\Merchant;
use EasyWeChat\Payment\Notify;
use EasyWeChat\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use EasyWeChat\Kernel\Support;

class NotifyTest extends TestCase
{
    public function testIsValid()
    {
        $merchant = new Merchant([
            'merchant_id' => '123456',
            'key' => 'foo',
        ]);

        $notify = \Mockery::mock(Notify::class.'[getNotify, isValid]', [$merchant])->makePartial();

        // build a collection vith invalid sign
        $mockInvalidResult = new Collection([
            'foo' => 'bar',
            'bax' => 123,
            'sign' => 'barsign',
        ]);

        $notify->expects()->getNotify()->andReturn($mockInvalidResult)->twice();

        $this->assertFalse($notify->isValid());

        // build a collection vith valid sign
        $mockValidResult = new Collection([
            'foo' => 'bar',
            'bax' => 123,
            'sign' => Support\generate_sign(['foo' => 'bar', 'bax' => 123], $merchant->key, 'md5'),
        ]);

        $notify->expects()->getNotify()->andReturn($mockValidResult)->twice();

        $this->assertTrue($notify->isValid());
    }

    public function testGetNotify()
    {
        $merchant = new Merchant([
            'merchant_id' => '123456',
            'key' => 'foo',
        ]);

        // test notify already has non-empty value.
        $notify = new DummnyClassForNotiryTest($merchant);
        $notify->setNotify(new Collection(['foo' => 'bar']));

        $notifyResult = $notify->getNotify();

        $this->assertNotEmpty($notifyResult);
        $this->assertInstanceOf(Collection::class, $notifyResult);
        $this->assertSame('bar', $notifyResult->foo);

        // teset normal.
        $xmlString = XML::build([
            'foo' => 'bar',
        ]);
        $request = new Request([], [], [], [], [], [], $xmlString);

        $notify = new Notify($merchant, $request);

        $notifyResult = $notify->getNotify();

        $this->assertNotEmpty($notifyResult);
        $this->assertInstanceOf(Collection::class, $notifyResult);
        $this->assertSame('bar', $notifyResult->foo);

        // test invalid request XML. (can't be parsed)
        $xmlString = 'foo';
        $request = new Request([], [], [], [], [], [], $xmlString);

        $notify = new Notify($merchant, $request);

        try {
            $notify->getNotify();
            $this->fail('No eaception is thrown.');
        } catch (\Throwable $e) {
            $this->assertStringStartsWith('Invalid request XML:', $e->getMessage());
            $this->assertSame(400, $e->getCode());
        }

        // test invalid request XML. (empty for example)
        $xmlString = XML::build([]);
        $request = new Request([], [], [], [], [], [], $xmlString);

        $notify = new Notify($merchant, $request);

        try {
            $notify->getNotify();
            $this->fail('No eaception is thrown.');
        } catch (\Throwable $e) {
            $this->assertSame('Invalid request XML.', $e->getMessage());
            $this->assertSame(400, $e->getCode());
        }
    }
}

class DummnyClassForNotiryTest extends Notify
{
    public function setNotify(Collection $notify)
    {
        $this->notify = $notify;
    }
}
