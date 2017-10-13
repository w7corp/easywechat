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
        $this->expectException(\Throwable::class);

        // test whether a Expection is thrown when the $attribute is not an array
        new Merchant(\Mockery::on(function ($attributes) {
            return !is_array($attributes);
        }));
    }

    public function testAttributes()
    {
        $merchant = new Merchant(['foo' => 'bar']);

        $this->assertSame('bar', $merchant->foo);
        $this->assertNull($merchant->baz);
    }
}
