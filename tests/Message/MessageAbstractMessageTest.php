<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Message;

use EasyWeChat\Message\AbstractMessage;
use EasyWeChat\Tests\TestCase;

class FooMessage extends AbstractMessage
{
    protected $properties = ['foo', 'bar'];
}

class MessageAbstractMessageTest extends TestCase
{
    /**
     * Test __get().
     */
    public function testGetterAndSetter()
    {
        $foo = new FooMessage(['foo' => 'overtrue']);

        $this->assertSame('overtrue', $foo->foo);

        // normal
        $foo->foo = 'hello';
        $foo->bar = 'barbar';
        $this->assertSame('hello', $foo->foo);
        $this->assertSame('barbar', $foo->bar);

        // property
        $foo->id = 6;
        $this->assertSame(6, $foo->id);

        // non-exists
        $foo->nonExists = 'hello';
        $this->assertNull($foo->nonExists);
    }
}
