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

use EasyWeChat\Kernel\Support\Arr;
use EasyWeChat\Kernel\Support\Collection;
use EasyWeChat\Tests\TestCase;
use stdClass;

class ArrTest extends TestCase
{
    public function testAdd()
    {
        $array = Arr::add(['name' => 'EasyWeChat'], 'price', 100);
        $this->assertSame(['name' => 'EasyWeChat', 'price' => 100], $array);
    }

    public function testCrossJoin()
    {
        // Single dimension
        $this->assertSame(
            [[1, 'a'], [1, 'b'], [1, 'c']],
            Arr::crossJoin([1], ['a', 'b', 'c'])
        );
        // Square matrix
        $this->assertSame(
            [[1, 'a'], [1, 'b'], [2, 'a'], [2, 'b']],
            Arr::crossJoin([1, 2], ['a', 'b'])
        );
        // Rectangular matrix
        $this->assertSame(
            [[1, 'a'], [1, 'b'], [1, 'c'], [2, 'a'], [2, 'b'], [2, 'c']],
            Arr::crossJoin([1, 2], ['a', 'b', 'c'])
        );
        // 3D matrix
        $this->assertSame(
            [
                [1, 'a', 'I'], [1, 'a', 'II'], [1, 'a', 'III'],
                [1, 'b', 'I'], [1, 'b', 'II'], [1, 'b', 'III'],
                [2, 'a', 'I'], [2, 'a', 'II'], [2, 'a', 'III'],
                [2, 'b', 'I'], [2, 'b', 'II'], [2, 'b', 'III'],
            ],
            Arr::crossJoin([1, 2], ['a', 'b'], ['I', 'II', 'III'])
        );
        // With 1 empty dimension
        $this->assertSame([], Arr::crossJoin([], ['a', 'b'], ['I', 'II', 'III']));
        $this->assertSame([], Arr::crossJoin([1, 2], [], ['I', 'II', 'III']));
        $this->assertSame([], Arr::crossJoin([1, 2], ['a', 'b'], []));
        // With empty arrays
        $this->assertSame([], Arr::crossJoin([], [], []));
        $this->assertSame([], Arr::crossJoin([], []));
        $this->assertSame([], Arr::crossJoin([]));
        // Not really a proper usage, still, test for preserving BC
        $this->assertSame([[]], Arr::crossJoin());
    }

    public function testDivide()
    {
        list($keys, $values) = Arr::divide(['name' => 'EasyWeChat']);
        $this->assertSame(['name'], $keys);
        $this->assertSame(['EasyWeChat'], $values);
    }

    public function testDot()
    {
        $array = Arr::dot(['foo' => ['bar' => 'baz']]);
        $this->assertSame(['foo.bar' => 'baz'], $array);
        $array = Arr::dot([]);
        $this->assertSame([], $array);
        $array = Arr::dot(['foo' => []]);
        $this->assertSame(['foo' => []], $array);
        $array = Arr::dot(['foo' => ['bar' => []]]);
        $this->assertSame(['foo.bar' => []], $array);
    }

    public function testExcept()
    {
        $array = ['name' => 'EasyWeChat', 'price' => 100];
        $array = Arr::except($array, ['price']);
        $this->assertSame(['name' => 'EasyWeChat'], $array);
    }

    public function testExists()
    {
        $this->assertTrue(Arr::exists([1], 0));
        $this->assertTrue(Arr::exists([null], 0));
        $this->assertTrue(Arr::exists(['a' => 1], 'a'));
        $this->assertTrue(Arr::exists(['a' => null], 'a'));
        $this->assertFalse(Arr::exists([1], 1));
        $this->assertFalse(Arr::exists([null], 1));
        $this->assertFalse(Arr::exists(['a' => 1], 0));
    }

    public function testFirst()
    {
        $array = [100, 200, 300];
        $value = Arr::first($array, function ($value) {
            return $value >= 150;
        });
        $this->assertSame(200, $value);
        $this->assertSame(100, Arr::first($array));

        $this->assertSame('default', Arr::first([], null, 'default'));

        $this->assertSame('default', Arr::first([], function () {
            return false;
        }, 'default'));
    }

    public function testLast()
    {
        $array = [100, 200, 300];
        $last = Arr::last($array, function ($value) {
            return $value < 250;
        });
        $this->assertSame(200, $last);
        $last = Arr::last($array, function ($value, $key) {
            return $key < 2;
        });
        $this->assertSame(200, $last);
        $this->assertSame(300, Arr::last($array));
    }

    public function testFlatten()
    {
        // Flat arrays are unaffected
        $array = ['#foo', '#bar', '#baz'];
        $this->assertSame(['#foo', '#bar', '#baz'], Arr::flatten(['#foo', '#bar', '#baz']));
        // Nested arrays are flattened with existing flat items
        $array = [['#foo', '#bar'], '#baz'];
        $this->assertSame(['#foo', '#bar', '#baz'], Arr::flatten($array));
        // Flattened array includes "null" items
        $array = [['#foo', null], '#baz', null];
        $this->assertSame(['#foo', null, '#baz', null], Arr::flatten($array));
        // Sets of nested arrays are flattened
        $array = [['#foo', '#bar'], ['#baz']];
        $this->assertSame(['#foo', '#bar', '#baz'], Arr::flatten($array));
        // Deeply nested arrays are flattened
        $array = [['#foo', ['#bar']], ['#baz']];
        $this->assertSame(['#foo', '#bar', '#baz'], Arr::flatten($array));
        // Nested arrays are flattened alongside arrays
        $array = [new Collection(['#foo', '#bar']), ['#baz']];
        $this->assertSame(['#foo', '#bar', '#baz'], Arr::flatten($array));
        // Nested arrays containing plain arrays are flattened
        $array = [new Collection(['#foo', ['#bar']]), ['#baz']];
        $this->assertSame(['#foo', '#bar', '#baz'], Arr::flatten($array));
        // Nested arrays containing arrays are flattened
        $array = [['#foo', new Collection(['#bar'])], ['#baz']];
        $this->assertSame(['#foo', '#bar', '#baz'], Arr::flatten($array));
        // Nested arrays containing arrays containing arrays are flattened
        $array = [['#foo', new Collection(['#bar', ['#zap']])], ['#baz']];
        $this->assertSame(['#foo', '#bar', '#zap', '#baz'], Arr::flatten($array));
    }

    public function testFlattenWithDepth()
    {
        // No depth flattens recursively
        $array = [['#foo', ['#bar', ['#baz']]], '#zap'];
        $this->assertSame(['#foo', '#bar', '#baz', '#zap'], Arr::flatten($array));
        // Specifying a depth only flattens to that depth
        $array = [['#foo', ['#bar', ['#baz']]], '#zap'];
        $this->assertSame(['#foo', ['#bar', ['#baz']], '#zap'], Arr::flatten($array, 1));
        $array = [['#foo', ['#bar', ['#baz']]], '#zap'];
        $this->assertSame(['#foo', '#bar', ['#baz'], '#zap'], Arr::flatten($array, 2));
    }

    public function testGet()
    {
        $array = ['products.item' => ['price' => 100]];
        $this->assertSame(['price' => 100], Arr::get($array, 'products.item'));
        $array = ['products' => ['item' => ['price' => 100]]];
        $value = Arr::get($array, 'products.item');
        $this->assertSame(['price' => 100], $value);
        // Test null array values
        $array = ['foo' => null, 'bar' => ['baz' => null]];
        $this->assertNull(Arr::get($array, 'foo', 'default'));
        $this->assertNull(Arr::get($array, 'bar.baz', 'default'));
        // Test null key returns the whole array
        $array = ['foo', 'bar'];
        $this->assertSame($array, Arr::get($array, null));
        // Test $array is empty and key is null
        $this->assertSame([], Arr::get([], null));
        $this->assertSame([], Arr::get([], null, 'default'));
    }

    public function testHas()
    {
        $array = ['products.item' => ['price' => 100]];
        $this->assertTrue(Arr::has($array, 'products.item'));
        $array = ['products' => ['item' => ['price' => 100]]];
        $this->assertTrue(Arr::has($array, 'products.item'));
        $this->assertTrue(Arr::has($array, 'products.item.price'));
        $this->assertFalse(Arr::has($array, 'products.foo'));
        $this->assertFalse(Arr::has($array, 'products.item.foo'));
        $array = ['foo' => null, 'bar' => ['baz' => null]];
        $this->assertTrue(Arr::has($array, 'foo'));
        $this->assertTrue(Arr::has($array, 'bar.baz'));
        $array = ['foo', 'bar'];
        $this->assertFalse(Arr::has($array, null));
        $this->assertFalse(Arr::has([], null));
        $array = ['products' => ['item' => ['price' => 100]]];
        $this->assertTrue(Arr::has($array, ['products.item']));
        $this->assertTrue(Arr::has($array, ['products.item', 'products.item.price']));
        $this->assertTrue(Arr::has($array, ['products', 'products']));
        $this->assertFalse(Arr::has($array, ['foo']));
        $this->assertFalse(Arr::has($array, []));
        $this->assertFalse(Arr::has($array, ['products.item', 'products.price']));
        $this->assertFalse(Arr::has([], [null]));
    }

    public function testIsAssoc()
    {
        $this->assertTrue(Arr::isAssoc(['a' => 'a', 0 => 'b']));
        $this->assertTrue(Arr::isAssoc([1 => 'a', 0 => 'b']));
        $this->assertTrue(Arr::isAssoc([1 => 'a', 2 => 'b']));
        $this->assertFalse(Arr::isAssoc([0 => 'a', 1 => 'b']));
        $this->assertFalse(Arr::isAssoc(['a', 'b']));
    }

    public function testOnly()
    {
        $array = ['name' => 'EasyWeChat', 'price' => 100, 'orders' => 10];
        $array = Arr::only($array, ['name', 'price']);
        $this->assertSame(['name' => 'EasyWeChat', 'price' => 100], $array);
    }

    public function testPrepend()
    {
        $array = Arr::prepend(['one', 'two', 'three', 'four'], 'zero');
        $this->assertSame(['zero', 'one', 'two', 'three', 'four'], $array);
        $array = Arr::prepend(['one' => 1, 'two' => 2], 0, 'zero');
        $this->assertSame(['zero' => 0, 'one' => 1, 'two' => 2], $array);
    }

    public function testPull()
    {
        $array = ['name' => 'EasyWeChat', 'price' => 100];
        $name = Arr::pull($array, 'name');
        $this->assertSame('EasyWeChat', $name);
        $this->assertSame(['price' => 100], $array);
        // Only works on first level keys
        $array = ['i@example.com' => 'Joe', 'jack@localhost' => 'Jane'];
        $name = Arr::pull($array, 'i@example.com');
        $this->assertSame('Joe', $name);
        $this->assertSame(['jack@localhost' => 'Jane'], $array);
        // Does not work for nested keys
        $array = ['emails' => ['i@example.com' => 'Joe', 'jack@localhost' => 'Jane']];
        $name = Arr::pull($array, 'emails.i@example.com');
        $this->assertNull($name);
        $this->assertSame(['emails' => ['i@example.com' => 'Joe', 'jack@localhost' => 'Jane']], $array);
    }

    public function testRandom()
    {
        $randomValue = Arr::random(['foo', 'bar', 'baz']);
        $this->assertContains($randomValue, ['foo', 'bar', 'baz']);
        $randomValues = Arr::random(['foo', 'bar', 'baz'], 1);
        $this->assertInternalType('array', $randomValues);
        $this->assertCount(1, $randomValues);
        $this->assertContains($randomValues[0], ['foo', 'bar', 'baz']);
        $randomValues = Arr::random(['foo', 'bar', 'baz'], 2);
        $this->assertInternalType('array', $randomValues);
        $this->assertCount(2, $randomValues);
        $this->assertContains($randomValues[0], ['foo', 'bar', 'baz']);
        $this->assertContains($randomValues[1], ['foo', 'bar', 'baz']);
    }

    public function testSet()
    {
        $array = ['products' => ['item' => ['price' => 100]]];
        Arr::set($array, 'products.item.price', 200);
        Arr::set($array, 'goods.item.price', 200);
        $this->assertSame(['products' => ['item' => ['price' => 200]], 'goods' => ['item' => ['price' => 200]]], $array);
    }

    public function testWhere()
    {
        $array = [100, '200', 300, '400', 500];
        $array = Arr::where($array, function ($value, $key) {
            return is_string($value);
        });
        $this->assertSame([1 => '200', 3 => '400'], $array);
    }

    public function testWhereKey()
    {
        $array = ['10' => 1, 'foo' => 3, 20 => 2];
        $array = Arr::where($array, function ($value, $key) {
            return is_numeric($key);
        });
        $this->assertSame(['10' => 1, 20 => 2], $array);
    }

    public function testForget()
    {
        $array = ['products' => ['item' => ['price' => 100]]];
        Arr::forget($array, null);
        $this->assertSame(['products' => ['item' => ['price' => 100]]], $array);
        $array = ['products' => ['item' => ['price' => 100]]];
        Arr::forget($array, []);
        $this->assertSame(['products' => ['item' => ['price' => 100]]], $array);
        $array = ['products' => ['item' => ['price' => 100]]];
        Arr::forget($array, 'products.item');
        $this->assertSame(['products' => []], $array);
        $array = ['products' => ['item' => ['price' => 100]]];
        Arr::forget($array, 'products.item.price');
        $this->assertSame(['products' => ['item' => []]], $array);
        $array = ['products' => ['item' => ['price' => 100]]];
        Arr::forget($array, 'products.final.price');
        $this->assertSame(['products' => ['item' => ['price' => 100]]], $array);
        $array = ['shop' => ['cart' => [150 => 0]]];
        Arr::forget($array, 'shop.final.cart');
        $this->assertSame(['shop' => ['cart' => [150 => 0]]], $array);
        $array = ['products' => ['item' => ['price' => ['original' => 50, 'taxes' => 60]]]];
        Arr::forget($array, 'products.item.price.taxes');
        $this->assertSame(['products' => ['item' => ['price' => ['original' => 50]]]], $array);
        $array = ['products' => ['item' => ['price' => ['original' => 50, 'taxes' => 60]]]];
        Arr::forget($array, 'products.item.final.taxes');
        $this->assertSame(['products' => ['item' => ['price' => ['original' => 50, 'taxes' => 60]]]], $array);
        $array = ['products' => ['item' => ['price' => 50], null => 'something']];
        Arr::forget($array, ['products.amount.all', 'products.item.price']);
        $this->assertSame(['products' => ['item' => [], null => 'something']], $array);
        // Only works on first level keys
        $array = ['i@example.com' => 'Joe', 'i@easywechat.com' => 'Jane'];
        Arr::forget($array, 'i@example.com');
        $this->assertSame(['i@easywechat.com' => 'Jane'], $array);
        // Does not work for nested keys
        $array = ['emails' => ['i@example.com' => ['name' => 'Joe'], 'jack@localhost' => ['name' => 'Jane']]];
        Arr::forget($array, ['emails.i@example.com', 'emails.jack@localhost']);
        $this->assertSame(['emails' => ['i@example.com' => ['name' => 'Joe']]], $array);
    }

    public function testWrap()
    {
        $string = 'a';
        $array = ['a'];
        $object = new stdClass();
        $object->value = 'a';
        $this->assertSame(['a'], Arr::wrap($string));
        $this->assertSame($array, Arr::wrap($array));
        $this->assertSame([$object], Arr::wrap($object));
    }
}
