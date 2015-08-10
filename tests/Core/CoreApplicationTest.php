<?php

use EasyWeChat\Core\AccessToken;
use EasyWeChat\Core\Application;
use EasyWeChat\Core\Http;
use EasyWeChat\Core\Input;
use EasyWeChat\Encryption\Cryptor;
use EasyWeChat\Support\Collection;
use EasyWeChat\Support\ServiceProvider;

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
     * Test _construct().
     */
    public function testConstruct()
    {
        $app = $this->getApp();

        $this->assertEquals(Collection::class, get_class($app->get('config')));
        $this->assertEquals(Http::class, get_class($app['http']));
        $this->assertEquals(Input::class, get_class($app['input']));
        $this->assertEquals(AccessToken::class, get_class($app['access_token']));
        $this->assertEquals(Cryptor::class, get_class($app['cryptor']));

        $this->assertEquals('overtrue', $app['config']['app_id']);
    }

    /**
     * Test setProviders().
     */
    public function testSetProviders()
    {
        $providers = [
            Mockery::namedMock('FooServiceProvider', ServiceProvider::class),
            Mockery::namedMock('BarServiceProvider', ServiceProvider::class),
        ];

        $app = $this->getApp();

        $app->setProviders($providers, true);

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
            Mockery::namedMock('BarzServiceProvider', 'stdClass'),
            Mockery::namedMock('BarServiceProvider', ServiceProvider::class),
        ];

        $app->setProviders($providers);
    }
}
