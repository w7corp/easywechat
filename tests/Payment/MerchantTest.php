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

use EasyWeChat\Payment\Merchant;
use EasyWeChat\Tests\TestCase;

class MerchantTest extends TestCase
{
    public function testErrorParams()
    {
        try {
            new Merchant('');
            $this->fail('No exception thrown.');
        } catch (\Throwable $e) {
            $this->assertInstanceOf(\TypeError::class, $e);
        }

        try {
            new Merchant(1);
            $this->fail('No exception thrown.');
        } catch (\Throwable $e) {
            $this->assertInstanceOf(\TypeError::class, $e);
        }
    }

    public function testAttributes()
    {
        $merchant = new Merchant(['foo' => 'bar']);

        $this->assertSame('bar', $merchant->foo);
    }
}
