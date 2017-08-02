<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Kernel\Support;

use EasyWeChat\Kernel\Support\AES;
use EasyWeChat\Tests\TestCase;

class AESTest extends TestCase
{
    public function testEncrypt()
    {
        $key = 'abcdefghijklmnopabcdefghijklmnop';
        $iv = substr($key, 0, 16);

        $expected = openssl_encrypt('foo', 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $this->assertSame($expected, AES::encrypt('foo', $key, $iv, OPENSSL_RAW_DATA));

        $this->assertSame('foo', AES::decrypt($expected, $key, $iv, OPENSSL_RAW_DATA));
    }
}
