<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Kernel;

use EasyWeChat\Kernel\Config;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\Tests\TestCase;
use GuzzleHttp\Client;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class ServiceContainerTest extends TestCase
{
    public function testBasicFeatures()
    {
        $container = new ServiceContainer();

        $this->assertEmpty($container->getProviders());

        // __set, __get, offsetGet
        $this->assertInstanceOf(Config::class, $container['config']);
        $this->assertInstanceOf(Config::class, $container->config);

        $this->assertInstanceOf(Client::class, $container['http_client']);
        $this->assertInstanceOf(Request::class, $container['request']);

        $container['foo'] = 'foo';
        $container->bar = 'bar';

        $this->assertSame('foo', $container['foo']);
        $this->assertSame('bar', $container['bar']);
    }

    public function testRegisterProviders()
    {
        $container = new DummyContainerForProviderTest();

        $this->assertSame('foo', $container['foo']);
    }

    public function testLoggerCreator()
    {
        $container = new DummyContainerForProviderTest();

        // null config
        $this->assertInstanceOf(Logger::class, $container['logger']);
        $this->assertSame(str_replace('\\', '.', strtolower(DummyContainerForProviderTest::class)), $container['logger']->getName());
        $this->assertCount(1, $container['logger']->getHandlers());
        $this->assertInstanceOf(ErrorLogHandler::class, $container['logger']->getHandlers()[0]);

        // log with handler
        $container = new ServiceContainer([
            'log' => [
                'handler' => new StreamHandler('/tmp/easywechat.log'),
            ],
        ]);

        $this->assertCount(1, $container['logger']->getHandlers());
        $this->assertInstanceOf(StreamHandler::class, $container['logger']->getHandlers()[0]);

        // log with file and level
        $container = new ServiceContainer([
            'log' => [
                'level' => 'debug',
                'file' => '/tmp/easywechat.log',
            ],
        ]);

        $this->assertCount(1, $container['logger']->getHandlers());
        $handler = $container['logger']->getHandlers()[0];
        $this->assertInstanceOf(StreamHandler::class, $handler);
        $this->assertSame('/tmp/easywechat.log', $handler->getUrl());
    }
}

class DummyContainerForProviderTest extends ServiceContainer
{
    protected $providers = [
        FooServiceProvider::class,
    ];
}

class FooServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['foo'] = function () {
            return 'foo';
        };
    }
}
