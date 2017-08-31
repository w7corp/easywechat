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
use EasyWeChat\Kernel\Support\Collection;
use EasyWeChat\Payment\Application;
use EasyWeChat\Payment\Notify;
use EasyWeChat\Payment\Traits\HandleNotify;
use EasyWeChat\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

class HandleNotiryTest extends TestCase
{
    /**
     * Make Application.
     *
     * @param array $config
     *
     * @return \EasyWeChat\Payment\Application
     */
    private function makeApp($config = [])
    {
        return new Application(array_merge([
            'app_id' => 'wx123456',
            'merchant_id' => 'foo-mcherant-id',
            'key' => 'foo-mcherant-key',
            'sub_appid' => 'foo-sub-appid',
            'sub_mch_id' => 'foo-sub-mch-id',
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
     * A callback for handleRefundNotify().
     *
     * @param $notify
     * @param $successful
     *
     * @return mixed
     */
    public function handleRefundNotifyCallback($notify, $successful)
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
            ->andReturn(false, true, true);

        $mockNotify->shouldReceive('getNotify')
            ->andReturn(
                new Collection(['result_code' => 'SUCCESS']),
                new Collection(['result_code' => 'FAIL'])
            );

        $mock->shouldReceive('getNotify')->andReturn($mockNotify);

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

        // result_code === 'FAIL'
        $this->assertInstanceOf(Response::class, $mock->handleNotify([$this, 'handleNotifyCallback']));
    }

    public function testRefundHandleNotify()
    {
        $app = $this->makeApp();

        $mock = \Mockery::mock(DummnyClassForHandleNotifyTest::class.'[getNotify, handleRefundNotify]', [$app])->shouldAllowMockingProtectedMethods()->makePartial();

        $mockNotify = \Mockery::mock(Notify::class, [$app['merchant']]);

        $mockNotify->shouldReceive('decryptReqInfo')
            ->andReturn($mockNotify);

        $mockNotify->shouldReceive('getNotify')
            ->andReturn(
                new Collection(['result_code' => 'SUCCESS']),
                new Collection(['result_code' => 'FAIL'])
            );

        $mock->shouldReceive('getNotify')->andReturn($mockNotify);

        // result_code === 'SUCCESS'
        $this->assertInstanceOf(Response::class, $mock->handleRefundNotify([$this, 'handleRefundNotifyCallback']));

        // result_code === 'FAIL'
        $this->assertInstanceOf(Response::class, $mock->handleRefundNotify([$this, 'handleRefundNotifyCallback']));
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
