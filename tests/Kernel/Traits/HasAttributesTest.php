<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Kernel\Traits;

use EasyWeChat\Kernel\Traits\HasAttributes;
use EasyWeChat\Tests\TestCase;

class HasAttributesTest extends TestCase
{
    public function testBasicFeatures()
    {
        $cls = new DummyClassForHasAttributesTest();

        $cls->setAttributes([
            'foo' => 'bar',
        ]);

        // required
        $this->assertSame(['foo'], $cls->getRequired());
        $this->assertTrue($cls->isRequired('foo'), 'assert foo is required.');

        // all
        $this->assertSame(['foo' => 'bar'], $cls->all());

        // not exists
        $this->assertNull($cls->not_exists);
        $this->assertSame('default', $cls->get('not_exists', 'default'));

        // getter
        $this->assertSame('bar', $cls->foo);
        $this->assertSame('bar', $cls->get('foo'));
        $this->assertSame('bar', $cls->getAttribute('foo'));

        // isset
        $this->assertTrue(isset($cls->foo), 'isset $cls->foo');

        // setter
        $cls->setAttribute('foo', 'new-foo');
        $this->assertSame('new-foo', $cls->foo);

        $cls->name = 'mock-name';
        $this->assertSame('mock-name', $cls->name);
        $this->assertSame('mock-name', $cls->get('name'));

        // set
        $cls->set('id', 'mock-id');
        $this->assertSame('mock-id', $cls->id);
    }

    public function testWith()
    {
        $cls = new DummyClassForHasAttributesTest();

        $cls->setAttributes([
            'foo' => 'bar',
        ]);

        $cls->with('bar', 'mock-value');
        $this->assertSame('mock-value', $cls->bar);

        $cls->withBar('bar');
        $cls->withName('overtrue');
        $this->assertSame('bar', $cls->bar);
        $this->assertSame('overtrue', $cls->name);

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Method "invalidMethodName" does not exists.');

        $cls->invalidMethodName('hello', 'world!');
    }

    public function testMerge()
    {
        $cls = new DummyClassForHasAttributesTest();

        $cls->setAttributes([
            'foo' => 'bar',
            'name' => 'easywechat',
        ]);

        $cls->merge([
            'age' => 27,
        ]);

        $this->assertTrue($cls->has('age'));
        $this->assertSame(27, $cls->get('age'));
    }

    public function testOnly()
    {
        $cls = new DummyClassForHasAttributesTest();

        $cls->setAttributes([
            'foo' => 'bar',
            'name' => 'easywechat',
            'age' => 27,
        ]);

        $this->assertSame([
            'foo' => 'bar',
        ], $cls->only('foo'));

        $this->assertSame([
            'foo' => 'bar',
            'age' => 27,
        ], $cls->only(['foo', 'age']));
    }
}

class DummyClassForHasAttributesTest
{
    use HasAttributes;

    protected $required = ['foo'];
}
