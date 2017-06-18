<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Foundation;

use EasyWeChat\Applications\Base\Core\Http;
use EasyWeChat\Config\Config as Config;
use EasyWeChat\Factory;
use EasyWeChat\Tests\TestCase;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class ApplicationTest extends TestCase
{
    /**
     * Test __construct().
     */
    public function testConstructor()
    {
        $app = new Factory(['foo' => 'bar']);

        $this->assertInstanceOf(Config::class, $app['config']);

        $providers = $app->getProviders();
        foreach ($providers as $provider) {
            $container = new Container();
            $container->register(new $provider());
            $container['config'] = $app->raw('config');
            $container['request'] = $app->raw('request');
            $container['cache'] = $app->raw('cache');

            foreach ($container->keys() as $providerName) {
                $this->assertSame($container->raw($providerName), $app->raw($providerName));
            }

            unset($container);
        }
    }

    public function testHttpDefaultOptions()
    {
        $app = new Factory([]);

        $this->assertSame(['timeout' => 5.0], Http::getDefaultOptions());

        $config = ['guzzle' => ['timeout' => 6]];
        $app = new Factory($config);

        $this->assertSame($config['guzzle'], Http::getDefaultOptions());
    }

    /**
     * test __set, __get.
     */
    public function testMagicMethod()
    {
        $app = new Factory(['foo' => 'bar']);

        $app->foo = 'bar';

        // getter setter
        $this->assertSame('bar', $app->foo);
    }

    /**
     * Test addProvider() and setProviders.
     */
    public function testProviders()
    {
        $app = new Factory(['foo' => 'bar']);

        $providers = $app->getProviders();

        $app->addProvider(\Mockery::mock(ServiceProviderInterface::class));

        $this->assertCount(count($providers) + 1, $app->getProviders());

        $app->setProviders(['foo', 'bar']);

        $this->assertSame(['foo', 'bar'], $app->getProviders());
    }

    public function testSetCustomAccessToken()
    {
        $config = [
            'app_id' => 'foo',
            'secret' => 'bar',
        ];

        $app = new Factory($config);

        $this->assertInstanceOf('EasyWeChat\Applications\OfficialAccount\Core\AccessToken', $app['official_account.access_token']);

        $app['official_account.access_token']->setToken('iamtokenhere');

        $this->assertSame('iamtokenhere', $app['official_account.access_token']->getToken());
    }

    public function testStaticCall()
    {
        $weworkInstances = [
            Factory::weWork(['client_id' => 'corpid@123', 'client_secret' => 'corpsecret@123', 'debug' => true]),
            Factory::make('weWork', ['debug' => true, 'client_id' => 'corpid@123', 'client_secret' => 'corpsecret@123']),
        ];
        foreach ($weworkInstances as $instance) {
            $this->assertInstanceOf('EasyWeChat\Applications\WeWork\Application', $instance);
            $expected = [
                'debug' => true,
                'client_id' => 'corpid@123',
                'client_secret' => 'corpsecret@123',
            ];
            $this->assertArraySubset($expected, $instance->fetch('config')->all());
        }

        $officialAccountInstances = [
            Factory::officialAccount(['appid' => 'appid@456']),
            Factory::make('officialAccount', ['appid' => 'appid@456']),
        ];
        foreach ($officialAccountInstances as $instance) {
            $this->assertInstanceOf('EasyWeChat\Applications\OfficialAccount\Application', $instance);
            $this->assertArraySubset(['appid' => 'appid@456'], $instance->fetch('config')->all());
        }

        $openPlatformInstances = [
            Factory::openPlatform(['appid' => 'appid@789']),
            Factory::make('openPlatform', ['appid' => 'appid@789']),
        ];
        foreach ($openPlatformInstances as $instance) {
            $this->assertInstanceOf('EasyWeChat\Applications\OpenPlatform\Application', $instance);
            $this->assertArraySubset(['appid' => 'appid@789'], $instance->fetch('config')->all());
        }

        $miniProgramInstances = [
            Factory::miniProgram(['appid' => 'appid@890']),
            Factory::make('miniProgram', ['appid' => 'appid@890']),
        ];
        foreach ($miniProgramInstances as $instance) {
            $this->assertInstanceOf('EasyWeChat\Applications\MiniProgram\MiniProgram', $instance);
            $this->assertArraySubset(['appid' => 'appid@890'], $instance->fetch('config')->all());
        }
    }
}
