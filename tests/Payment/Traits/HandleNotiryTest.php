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

use EasyWeChat\Kernel\Exceptions\Exception;
use EasyWeChat\Kernel\Http\Response;
use EasyWeChat\Kernel\Support\Collection;
use EasyWeChat\Payment\Application;
use EasyWeChat\Payment\Notify;
use EasyWeChat\Payment\Traits\HandleNotify;
use EasyWeChat\Tests\TestCase;

class HandleNotiryTest extends TestCase
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

    /**
     * A callback for handleNotify().
     *
     * @param $notify
     * @param $successful
     *
     * @return mixed
     */
    public function handleNotifyCallback($notify, $successful)
    {
        return $successful;
    }

    /**
     * A callback for handleScanNotify().
     *
     * @param $produceId
     * @param $openid
     * @param $notify
     *
     * @return string
     *
     * @throws \Exception
     */
    public function handleScanNotifyCallback($produceId, $openid, $notify)
    {
        if ($produceId) {
            return 'foo';
        }

        throw new Exception('No product_id given.');
    }

    public function testHandleNotify()
    {
        $app = $this->makeApp();

        $mock = \Mockery::mock(DummnyClassForHandleNotifyTest::class.'[getNotify, handleNotify]', [$app])->shouldAllowMockingProtectedMethods()->makePartial();

        $mockNotify = \Mockery::mock(Notify::class, [$app['merchant']]);

        // mock Notify.
        $mockNotify->shouldReceive('isValid')
            ->times(3)
            ->andReturn(false, true, true);

        $mockNotify->shouldReceive('getNotify')
            ->twice()
            ->andReturn(
                new Collection(['result_code' => 'SUCCESS']),
                new Collection(['result_code' => 'FAILURE'])
            );

        $mock->shouldReceive('getNotify')
            ->times(3)
            ->andReturn($mockNotify);

        // $notify->isValid() === false
        try {
            $mock->handleNotify([$this, 'handleNotifyCallback']);
            $this->fail('No expection was thrown.');
        } catch (\Exception $e) {
            $this->assertSame('Invalid request payloads.', $e->getMessage());
            $this->assertSame(400, $e->getCode());
        }

        // result_code === 'SUCCESS'
        $this->assertInstanceOf(Response::class, $mock->handleNotify([$this, 'handleNotifyCallback']));

        // result_code === 'SUCCESS'
        $this->assertInstanceOf(Response::class, $mock->handleNotify([$this, 'handleNotifyCallback']));
    }

    public function testHandleScanNotify()
    {
        $app = $this->makeApp();

        $mock = \Mockery::mock(DummnyClassForHandleNotifyTest::class.'[getNotify, handleScanNotify]', [$app])->shouldAllowMockingProtectedMethods()->makePartial();

        $mockNotify = \Mockery::mock(Notify::class, [$app['merchant']]);

        // mock Notify.
        $mockNotify->shouldReceive('isValid')
            ->times(3)
            ->andReturn(false, true, true);

        $mockNotify->shouldReceive('getNotify')
            ->twice()
            ->andReturn(
                new Collection(['product_id' => 'foo', 'openid' => 'wx12345']),
                new Collection(['product_id' => null, 'oepnid' => null])
            );

        $mock->shouldReceive('getNotify')
            ->times(3)
            ->andReturn($mockNotify);

        // $notify->isValid() === false
        try {
            $mock->handleScanNotify([$this, 'handleScanNotifyCallback']);
            $this->fail('No expection was thrown.');
        } catch (\Exception $e) {
            $this->assertSame('Invalid request payloads.', $e->getMessage());
            $this->assertSame(400, $e->getCode());
        }

        // product_id and openid is valid.
        $this->assertInstanceOf(Response::class, $mock->handleScanNotify([$this, 'handleScanNotifyCallback']));

        // product_id or oepndid is not valid.
        $this->assertInstanceOf(Response::class, $mock->handleScanNotify([$this, 'handleScanNotifyCallback']));
    }

    public function testGetNotify()
    {
        $app = $this->makeApp();

        $mock = \Mockery::mock(DummnyClassForHandleNotifyTest::class.'[getNotify]', [$app])->shouldAllowMockingProtectedMethods()->makePartial();

        $this->assertInstanceOf(Notify::class, $mock->getNotify());
    }
}

class DummnyClassForHandleNotifyTest
{
    use HandleNotify;

    public $app;

    /**
     * DummnyClassForWorksInSandboxTest constructor.
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}
