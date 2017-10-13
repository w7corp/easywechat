<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Payment\Traits;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Support\XML;
use EasyWeChat\Payment\Application;
use EasyWeChat\Payment\Traits\WorksInSandbox;
use EasyWeChat\Tests\TestCase;

class WorkInSandboxTest extends TestCase
{
    /**
     * Make Application.
     *
     * @param array $config
     */
    private function makeApp($config = [])
    {
        return new Application(array_merge([
            'merchant' => [
                'id' => 'foo',
                'merchant_id' => 'bar',
                'sub_appid' => 'foo_sub_appid',
                'sub_mch_id' => 'foo_sub_mch_id',
            ],
        ], $config));
    }

    public function testSandboxMode()
    {
        $app = $this->makeApp();

        $mock = \Mockery::mock(DummnyClassForWorksInSandboxTest::class.'[sadboxMode]', [$app])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        // assert instance type
        $this->assertInstanceOf(DummnyClassForWorksInSandboxTest::class, $mock->sandboxMode());
        $this->assertInstanceOf(DummnyClassForWorksInSandboxTest::class, $mock->sandboxMode(true));

        $this->assertFalse($mock->sandboxMode()->inSandbox);
        $this->assertTrue($mock->sandboxMode(true)->inSandbox);
        $this->assertFalse($mock->sandboxMode(false)->inSandbox);
    }

    public function testWrapApi()
    {
        $app = $this->makeApp();

        $mock = \Mockery::mock(DummnyClassForWorksInSandboxTest::class.'[sandboxMode, wrapApi]', [$app])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $this->assertSame('foo', $mock->wrapApi('foo'));
        $this->assertSame('foo', $mock->sandboxMode()->wrapApi('foo'));

        // assert return value when it's in sandbox mode.
        $this->assertSame('sandboxnew/foo', $mock->sandboxMode(true)->wrapApi('foo'));
    }

    public function testGetSignKey()
    {
        $app = $this->makeApp();

        $mock = \Mockery::mock(DummnyClassForWorksInSandboxTest::class.'[getSignKey, getSandboxSignKey]', [$app])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $mock->expects()->getSandboxSignKey()->andReturn('mock-sign-key');

        // inSandbox === false
        $this->assertSame($app['merchant']->key, $mock->sandboxMode(false)->getSignKey('foo'));

        // inSandbox === true
        $this->assertSame('mock-sign-key', $mock->sandboxMode(true)->getSignKey('foo'));

        // $api === $this->signKeyEndpoint
        $this->assertSame($app['merchant']->key, $mock->sandboxMode(false)->getSignKey('sandboxnew/pay/getsignkey'));
    }

    public function testGetSandboxSignKey()
    {
        $app = $this->makeApp();

        $mock = \Mockery::mock(DummnyClassForWorksInSandboxTest::class.'[getSandboxSignKey, getSignKeyFromServer, getCache]', [$app])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $mock->expects()->getSignKeyFromServer()->andReturn('mock-signkey');

        // Important! clear cache firstly.
        $mock->getCache()->clear();

        $this->assertSame('mock-signkey', $mock->getSandboxSignKey());

        // $this->signKey has a value and no cached value.
        $mock->signKey = 'foo_sign_key';
        $this->assertSame('foo_sign_key', $mock->getSandboxSignKey());

        // $this->signKey === null but has cached value.
        $mock->signKey = null;
        $mock->getCache()->set($mock->getCacheKey(), 'sign-key-in-cache');
        $this->assertSame('sign-key-in-cache', $mock->getSandboxSignKey());
    }

    public function testGetSignKeyFromServer()
    {
        $app = $this->makeApp();

        $mock = \Mockery::mock(DummnyClassForWorksInSandboxTest::class.'[requestRaw, getSignKeyFromServer]', [$app])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        // return_code === SUCCESS
        $successXmlString = XML::build([
            'return_code' => 'SUCCESS',
            'return_msg' => 'SUCCESS',
            'sandbox_signkey' => 'bar_signkey',
        ]);

        // return_code === FAILURE
        $failureXmlString = XML::build([
            'return_code' => 'FAILURE',
            'return_msg' => 'failure msg',
        ]);

        $mock->shouldReceive('requestRaw->getBody')
            ->twice()
            ->andReturn($successXmlString, $failureXmlString);

        $this->assertSame('bar_signkey', $mock->getSignKeyFromServer());

        // when return_code !== 'SUCCESS', an Exception should be thrown.
        try {
            $mock->getSignKeyFromServer();
        } catch (\Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame('failure msg', $e->getMessage());
        }
    }

    public function testGetCacheKey()
    {
        $app = $this->makeApp();

        $mock = \Mockery::mock(DummnyClassForWorksInSandboxTest::class.'[getCacheKey]', [$app])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $this->assertSame('easywechat.payment.sandbox.'.$app['merchant']->merchant_id, $mock->getCacheKey());
    }
}

class DummnyClassForWorksInSandboxTest
{
    use WorksInSandbox;

    public $app;

    /**
     * DummnyClassForWorksInSandboxTest constructor.
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function __get($propertyName)
    {
        return $this->$propertyName ?? null;
    }

    public function __set($propertyName, $value)
    {
        $this->$propertyName = $value;
    }
}
