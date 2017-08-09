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

use EasyWeChat\Payment\Order;
use EasyWeChat\Tests\TestCase;

class OrderTest extends TestCase
{
    public function testErrorParams()
    {
        try {
            new Order('');
            $this->fail('No exception thrown.');
        } catch (\Throwable $e) {
            $this->assertInstanceOf(\TypeError::class, $e);
        }

        try {
            new Order(1);
            $this->fail('No exception thrown.');
        } catch (\Throwable $e) {
            $this->assertInstanceOf(\TypeError::class, $e);
        }
    }

    public function testAttributes()
    {
        $order = new Order(['foo' => 'bar']);

        $this->assertSame('bar', $order->foo);
    }
}
