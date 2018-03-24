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

use EasyWeChat\Kernel\Support\Collection;
use EasyWeChat\Tests\TestCase;

class CollectionTest extends TestCase
{
    public function testOnly()
    {
        $data = new Collection(['first' => 'overtrue', 'last' => 'Otwell', 'email' => 'overtrue@easywechat.com']);
        $this->assertSame(['first' => 'overtrue'], $data->only(['first', 'missing'])->all());
        $this->assertSame(['first' => 'overtrue', 'email' => 'overtrue@easywechat.com'], $data->only(['first', 'email'])->all());
    }

    public function testExcept()
    {
        $data = new Collection(['first' => 'overtrue', 'last' => 'Otwell', 'email' => 'overtrue@easywechat.com']);
        $this->assertSame(['first' => 'overtrue'], $data->except(['last', 'email', 'missing'])->all());
        $this->assertSame(['first' => 'overtrue'], $data->except('last', 'email', 'missing')->all());
        $this->assertSame(['first' => 'overtrue', 'email' => 'overtrue@easywechat.com'], $data->except(['last'])->all());
        $this->assertSame(['first' => 'overtrue', 'email' => 'overtrue@easywechat.com'], $data->except('last')->all());
    }

    public function testMerge()
    {
        $c = new Collection(['name' => 'Hello']);
        $this->assertSame(['name' => 'Hello', 'id' => 1], $c->merge(['id' => 1])->all());
    }

    public function testHas()
    {
        $c = new Collection(['name' => 'Hello']);

        $this->assertTrue($c->has('name'));
        $this->assertFalse($c->has('overtrue'));
    }

    public function testFirst()
    {
        $c = new Collection(['name' => 'Hello', 'age' => 25]);

        $this->assertSame('Hello', $c->first());
    }

    public function testLast()
    {
        $c = new Collection(['name' => 'Hello', 'age' => 25]);

        $this->assertSame(25, $c->last());
    }

    public function testAdd()
    {
        $c = new Collection(['name' => 'Hello', 'age' => 25]);

        $c->add('foo', 'bar');

        $this->assertSame('bar', $c->last());
        $this->assertSame('bar', $c->foo);
    }

    public function testForget()
    {
        $c = new Collection(['name' => 'Hello', 'age' => 25]);

        $c->forget('age');

        $this->assertSame('Hello', $c->last());
        $this->assertNull($c->age);
    }

    public function testToArray()
    {
        $c = new Collection(['name' => 'Hello', 'age' => 25]);

        $this->assertSame(['name' => 'Hello', 'age' => 25], $c->toArray());
    }

    public function testToJson()
    {
        $c = new Collection(['name' => 'Hello', 'age' => 25]);

        $this->assertSame(json_encode(['name' => 'Hello', 'age' => 25]), $c->toJson());
        $this->assertSame(json_encode(['name' => 'Hello', 'age' => 25]), (string) $c);
        $this->assertSame(json_encode(['name' => 'Hello', 'age' => 25]), json_encode($c));
    }

    public function testSerialize()
    {
        $c = new Collection(['name' => 'Hello', 'age' => 25]);

        $sc = serialize($c);
        $c = unserialize($sc);

        $this->assertSame(['name' => 'Hello', 'age' => 25], $c->all());
    }

    public function testGetIterator()
    {
        $c = new Collection(['name' => 'Hello', 'age' => 25]);

        $this->assertInstanceOf(\ArrayIterator::class, $c->getIterator());

        $this->assertSame(['name' => 'Hello', 'age' => 25], $c->getIterator()->getArrayCopy());
    }

    public function testCount()
    {
        $c = new Collection(['name' => 'Hello', 'age' => 25]);

        $this->assertCount(2, $c);
    }

    public function testBasicFeatures()
    {
        $c = new Collection(['name' => 'Hello', 'age' => 25]);

        $this->assertSame('Hello', $c->name);
        $this->assertSame('Hello', $c['name']);
        $this->assertSame('Hello', $c->get('name'));

        $this->assertTrue(isset($c['name']), 'isset $c[\'name\']');
        $this->assertTrue(isset($c->name), 'isset $c->name');
        $this->assertFalse(isset($c['not-exists']), 'isset $c[\'not-exists\']');
        $this->assertFalse(isset($c->not_exists), 'isset $c->not_exists');

        $c->name = 'new value';
        $this->assertSame('new value', $c->name);

        $c->set('foo', 'bar');
        $this->assertSame('bar', $c->foo);

        unset($c['foo']);
        $c->set('title', 'mock-title');
        unset($c->title);
        $this->assertFalse(isset($c->title), 'isset $c->title');

        $c['name'] = 'Hello';
        $this->assertSame(['name' => 'Hello', 'age' => 25], $c->__set_state());
    }
}
