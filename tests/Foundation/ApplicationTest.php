<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\Foundation\Application;
use EasyWeChat\Foundation\Config;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class ApplicationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test __construct().
     */
    public function testConstructor()
    {
        $app = new Application(['foo' => 'bar']);

        $this->assertInstanceOf(Config::class, $app['config']);

        $providers = $app->getProviders();
        foreach ($providers as $provider) {
            $container = new Container();
            $container->register(new $provider());
            $container['config'] = $app->raw('config');
            $container['access_token'] = $app->raw('access_token');
            $container['request'] = $app->raw('request');
            $container['cache'] = $app->raw('cache');

            foreach ($container->keys() as $providerName) {
                $this->assertEquals($container->raw($providerName), $app->raw($providerName));
            }

            unset($container);
        }
    }

    /**
     * test __set, __get.
     */
    public function testMagicMethod()
    {
        $app = new Application(['foo' => 'bar']);

        $app->foo = 'bar';

        // getter setter
        $this->assertEquals('bar', $app->foo);
    }

    /**
     * Test addProvider() and setProviders.
     */
    public function testProviders()
    {
        $app = new Application(['foo' => 'bar']);

        $providers = $app->getProviders();

        $app->addProvider(Mockery::mock(ServiceProviderInterface::class));

        $this->assertCount(count($providers) + 1, $app->getProviders());

        $app->setProviders(['foo', 'bar']);

        $this->assertSame(['foo', 'bar'], $app->getProviders());
    }
}
