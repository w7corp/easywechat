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

use EasyWeChat\Kernel\Support\Str;
use EasyWeChat\Tests\TestCase;

class StrTest extends TestCase
{
    public function testCamel()
    {
        $this->assertSame('fooBar', Str::camel('FooBar'));
        $this->assertSame('fooBar', Str::camel('FooBar')); // cached
        $this->assertSame('fooBar', Str::camel('foo_bar'));
        $this->assertSame('fooBar', Str::camel('_foo_bar'));
        $this->assertSame('fooBar', Str::camel('_foo_bar_'));
    }

    public function testStudly()
    {
        $this->assertSame('FooBar', Str::studly('fooBar'));
        $this->assertSame('FooBar', Str::studly('_foo_bar'));
        $this->assertSame('FooBar', Str::studly('_foo_bar_'));
        $this->assertSame('FooBar', Str::studly('_foo_bar_'));
    }

    public function testSnake()
    {
        $this->assertSame('foo_bar', Str::snake('fooBar'));
        $this->assertSame('foo_bar', Str::snake('fooBar')); // cached
        $this->assertSame('foo_bar', Str::snake('_Foo_bar'));
        $this->assertSame('foo_bar', Str::snake('FooBar'));
        $this->assertSame('foo_bar', Str::snake('Foo_bar_'));
    }

    public function testTitle()
    {
        $this->assertSame('Welcome Back', Str::title('welcome back'));
    }

    public function testRandom()
    {
        $this->assertInternalType('string', Str::random(10));
        $this->assertTrue(16 === strlen(Str::random()));
    }

    public function testQuickRandom()
    {
        $this->assertInternalType('string', Str::quickRandom(10));
        $this->assertTrue(16 === strlen(Str::quickRandom()));
    }

    public function testUpper()
    {
        $this->assertSame('USERNAME', Str::upper('username'));
        $this->assertSame('USERNAME', Str::upper('userNaMe'));
    }
}
