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
        $cls->merge(['hello' => 'world']);
        $this->assertSame('bar', $cls->bar);
        $this->assertSame('overtrue', $cls->name);
        $this->assertSame('world', $cls->hello);
    }
}

class DummyClassForHasAttributesTest
{
    use HasAttributes;

    protected $required = ['foo'];
}
