<?php

use EasyWeChat\Message\AbstractMessage;

class FooMessage extends AbstractMessage
{
    protected $properties = ['foo', 'bar'];
}

class MessageAbstractMessageTest extends TestCase
{
    /**
     * Test __get()
     */
    public function testGetterAndSetter()
    {
        $foo = new FooMessage(['foo' => 'overtrue']);

        $this->assertEquals('overtrue', $foo->foo);

        // normal
        $foo->foo = 'hello';
        $foo->bar = 'barbar';
        $this->assertEquals('hello', $foo->foo);
        $this->assertEquals('barbar', $foo->bar);

        // property
        $foo->id = 6;
        $this->assertEquals(6, $foo->id);

        // non-exists
        $foo->nonExists = 'hello';
        $this->assertNull($foo->nonExists);
    }
}