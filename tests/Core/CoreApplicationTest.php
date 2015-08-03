<?php

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
     * Test _construct().
     */
    public function testConstruct()
    {
        $app = $this->getApp();

        $this->assertEquals('EasyWeChat\Support\Collection', get_class($app->get('config')));
        $this->assertEquals('EasyWeChat\Core\Http', get_class($app['http']));
        $this->assertEquals('EasyWeChat\Core\Input', get_class($app['input']));
        $this->assertEquals('EasyWeChat\Core\AccessToken', get_class($app['access_token']));
        $this->assertEquals('EasyWeChat\Encryption\Cryptor', get_class($app['cryptor']));

        $this->assertEquals('overtrue', $app['config']['app_id']);
    }

    /**
     * Test setProviders().
     */
    public function testSetProviders()
    {
        $providers = [
            Mockery::namedMock('FooServiceProvider', 'EasyWeChat\Support\ServiceProvider'),
            Mockery::namedMock('BarServiceProvider', 'EasyWeChat\Support\ServiceProvider'),
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
            Mockery::namedMock('BarServiceProvider', 'EasyWeChat\Support\ServiceProvider'),
        ];

        $app->setProviders($providers);
    }
}
