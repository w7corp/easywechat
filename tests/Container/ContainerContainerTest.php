<?php

use EasyWeChat\Container\Container;

class ContainerContainerTest extends TestCase
{
    /**
     * Test bind();.
     */
    public function testBind()
    {
        $app = new Container();

        // string
        $app->bind('foo', 'bar');
        $this->assertArrayHasKey('foo', $app->getBindings());
        $this->assertEquals('bar', $app->getBindings()['foo']['concrete']);
        $this->assertFalse($app->getBindings()['foo']['share']);

        // array
        $app->bind('arr', ['bar']);
        $this->assertArrayHasKey('arr', $app->getBindings());
        $this->assertEquals(['bar'], $app->getBindings()['arr']['concrete']);

        // callback
        $app->bind('bar', function () { return 'bar'; });
        $this->assertArrayHasKey('bar', $app->getBindings());
        $this->assertEquals('Closure', get_class($app->getBindings()['bar']['concrete']));

        // shared
        $app->bind('shared_foo', function () { return new stdClass(); }, true);
        $this->assertTrue($app->getBindings()['shared_foo']['share']);

        // force bind
        $app->bind('foo', 'bar');
        $app->bind('foo', 'overtrue', false);
        $this->assertEquals('overtrue', $app->getBindings()['foo']['concrete']);

        // array access
        $app['overtrue'] = function () { return 'bar'; };
        $this->assertArrayHasKey('overtrue', $app->getBindings());

        //offset set
        $app['foobarbaz'] = 'something anothor.';
        $this->assertEquals('something anothor.', $app['foobarbaz']);

        // offset unset
        $app['foobarbaz'] = 'something anothor.';
        $this->assertEquals('something anothor.', $app['foobarbaz']);
        $this->assertTrue(isset($app['foobarbaz']));
        unset($app['foobarbaz']);
        $this->assertArrayNotHasKey('foobarbaz', $app->getBindings());

        // magic access
        $app->magic = function () { return 'magic access'; };

        $this->assertArrayHasKey('magic', $app->getBindings());

        $app->magic_access = 'something else.';
        $this->assertEquals('something else.', $app->magic_access);
    }

    /**
     * Test getIterator().
     */
    public function testGetIterator()
    {
        $app = new Container();

        // string
        $app->bind('foo', 'bar');
        $this->assertArrayHasKey('foo', $app->getBindings());
        $this->assertEquals('bar', $app->getBindings()['foo']['concrete']);
        $this->assertFalse($app->getBindings()['foo']['share']);

        // array
        $app->bind('arr', ['bar']);
        $this->assertArrayHasKey('arr', $app->getBindings());
        $this->assertEquals(['bar'], $app->getBindings()['arr']['concrete']);

        $arr = [];

        foreach ($app as $name => $service) {
            $arr[$name] = $service;
        }

        $this->assertEquals($arr, $app->getBindings());
    }

    /**
     * Test singleton().
     */
    public function testSingleton()
    {
        $app = new Container();

        // shared
        $app->singleton('foo', function () { return new stdClass(); }, true);
        $this->assertArrayHasKey('foo', $app->getBindings());
        $this->assertTrue($app->getBindings()['foo']['share']);
    }

    /**
     * Test isResolved().
     */
    public function testIsResolved()
    {
        $app = new Container();

        $this->assertFalse($app->isResolved('foo'));

        $app->singleton('foo', function () { return new stdClass(); });
        $foo = $app->get('foo');
        $this->assertEquals('stdClass', get_class($foo));

        $this->assertTrue($app->isResolved('foo'));
    }

    /**
     * Test isShared().
     */
    public function testIsShared()
    {
        $app = new Container();

        $app->bind('foo', function () { return new stdClass(); });
        $app->singleton('bar', function () { return new stdClass(); });

        $this->assertFalse($app->isShared('foo'));
        $this->assertTrue($app->isShared('bar'));
    }

    /**
     * Test get().
     *
     * @expectedException EasyWeChat\Core\Exceptions\UnboundServiceException
     */
    public function testGet()
    {
        $app = $this->getApp();

        $app->bind('foo', function () { return new stdClass(); });
        $app->singleton('bar', function () { return new stdClass(); });

        $this->assertEquals('stdClass', get_class($app->get('foo')));
        $this->assertEquals('stdClass', get_class($app->foo));
        $this->assertEquals('stdClass', get_class($app['foo']));
        $this->assertEquals('stdClass', get_class($app->get('bar')));

        // exception
        $app->get('barz');
    }
}
