<?php

use Mockery as m;
use EasyWeChat\Core\Application;

class CoreApplicationTest extends TestCase
{
    /**
     * Test bind without configuration.
     *
     * @expectedException EasyWeChat\Core\Exceptions\InvalidConfigException
     */
    public function testEmptyConfiguration()
    {
        $app = new Application([]);
    }

    /**
     * Test _construct()
     */
    public function testConstruct()
    {
        $app = $this->getApp();

        $this->assertEquals('EasyWeChat\Support\Collection', get_class($app->get('config')));
        $this->assertEquals('EasyWeChat\Core\Http', get_class($app['http']));
        $this->assertEquals('EasyWeChat\Core\AccessToken', get_class($app['access_token']));
        $this->assertEquals('EasyWeChat\Encryption\Cryptor', get_class($app['cryptor']));

        $this->assertEquals('overtrue', $app['config']['app_id']);
    }

    /**
     * Test bind();
     */
    public function testBind()
    {
        $app = $this->getApp();

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
        $app->bind('bar', function(){ return 'bar'; });
        $this->assertArrayHasKey('bar', $app->getBindings());
        $this->assertEquals('Closure', get_class($app->getBindings()['bar']['concrete']));

        // shared
        $app->bind('shared_foo', function(){ return new stdClass(); }, true);
        $this->assertTrue($app->getBindings()['shared_foo']['share']);

        // force bind
        $app->bind('foo', 'bar');
        $app->bind('foo', 'overtrue', false, false);
        $this->assertEquals('bar', $app->getBindings()['foo']['concrete']);
        $app->bind('foo', 'overtrue', false, true);
        $this->assertEquals('overtrue', $app->getBindings()['foo']['concrete']);
    }

    /**
     * Test singleton()
     */
    public function testSingleton()
    {
        $app = $this->getApp();

        // shared
        $app->singleton('foo', function(){ return new stdClass(); }, true);
        $this->assertArrayHasKey('foo', $app->getBindings());
        $this->assertTrue($app->getBindings()['foo']['share']);
    }

    /**
     * Test unBind()
     */
    public function testUnBind()
    {
        $app = $this->getApp();

        // shared
        $app->singleton('foo', function(){ return new stdClass(); }, true);
        $this->assertArrayHasKey('foo', $app->getBindings());
        $this->assertTrue($app->getBindings()['foo']['share']);

        $app->unBind('foo');
        $this->assertArrayNotHasKey('foo', $app->getBindings());
    }

    /**
     * Test isBound()
     */
    public function testIsBound()
    {
        $app = $this->getApp();

        $this->assertFalse($app->isBound('foo'));

        $app->singleton('foo', function(){ return new stdClass(); });
        $this->assertTrue($app->isBound('foo'));
    }

    /**
     * Test isResolved()
     */
    public function testIsResolved()
    {
        $app = $this->getApp();

        $this->assertFalse($app->isResolved('foo'));

        $app->singleton('foo', function(){ return new stdClass(); });
        $foo = $app->get('foo');
        $this->assertEquals('stdClass', get_class($foo));

        $this->assertTrue($app->isResolved('foo'));
    }

    /**
     * Test isShared()
     */
    public function testIsShared()
    {
        $app = $this->getApp();

        $app->bind('foo', function(){ return new stdClass(); });
        $app->singleton('bar', function(){ return new stdClass(); });

        $this->assertFalse($app->isShared('foo'));
        $this->assertTrue($app->isShared('bar'));
    }

    /**
     * Test setProviders()
     */
    public function testSetProviders()
    {
        $providers = [
            m::namedMock('FooServiceProvider', 'EasyWeChat\Support\ServiceProvider'),
            m::namedMock('BarServiceProvider', 'EasyWeChat\Support\ServiceProvider'),
        ];

        $app = $this->getApp();

        $app->setProviders($providers);

        $this->assertContains($providers[0], $app->getProviders());
        $this->assertContains($providers[1], $app->getProviders());
    }

    /**
     * Test setProviders() with invalid providers.
     *
     * @expectedException EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function testSetInvalidProviders()
    {
        $app = $this->getApp();

        $providers = [
            m::namedMock('BarzServiceProvider', 'stdClass'),
            m::namedMock('BarServiceProvider', 'EasyWeChat\Support\ServiceProvider'),
        ];

        $app->setProviders($providers);
    }

    /**
     * Test get()
     *
     * @expectedException EasyWeChat\Core\Exceptions\UnboundServiceException
     */
    public function testGet()
    {
        $app = $this->getApp();

        $app->bind('foo', function(){ return new stdClass(); });
        $app->singleton('bar', function(){ return new stdClass(); });

        $this->assertEquals('stdClass', get_class($app->get('foo')));
        $this->assertEquals('stdClass', get_class($app->foo));
        $this->assertEquals('stdClass', get_class($app['foo']));
        $this->assertEquals('stdClass', get_class($app->get('bar')));

        // exception
        $app->get('barz');
    }
}