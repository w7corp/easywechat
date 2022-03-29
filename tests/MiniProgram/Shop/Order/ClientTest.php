<?php

namespace EasyWeChat\Tests\MiniProgram\Shop\Order;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\MiniProgram\Shop\Order\Client;

/**
 * 自定义版交易组件开放接口
 *    订单接口
 *
 * @package EasyWeChat\Tests\MiniProgram\Shop\Basic
 * @author HaoLiang <haoliang@qiyuankeji.cn>
 */
class ClientTest extends TestCase
{
    /**
     * 检查场景值是否在支付校验范围内
     */
    public function testSceneCheck()
    {
        $client = $this->mockApiClient(Client::class);

        $scene = 1175;

        $client->expects()->httpPostJson('shop/scene/check', [
            'scene' => $scene
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->sceneCheck($scene));
    }

    /**
     * 生成订单
     */
    public function testAdd()
    {
        $client = $this->mockApiClient(Client::class);

        $outOrderId = '456abc';

        $client->expects()->httpPostJson('shop/order/add', [
            'out_order_id' => $outOrderId
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->create([
            'out_order_id' => $outOrderId
        ]));
    }

    /**
     * 生成订单
     */
    public function testCreate()
    {
        $client = $this->mockApiClient(Client::class);

        $outOrderId = '456abc';

        $client->expects()->httpPostJson('shop/order/add', [
            'out_order_id' => $outOrderId
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->create([
            'out_order_id' => $outOrderId
        ]));
    }

    /**
     * 获取订单详情
     */
    public function testGet()
    {
        $client = $this->mockApiClient(Client::class);

        $openid = 'abcdefghijklmn123';

        $outOrderId = '456abc';

        $client->expects()->httpPostJson('shop/order/get', [
            'openid' => $openid,
            'out_order_id' => $outOrderId,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->get($openid, [
            'out_order_id' => $outOrderId
        ]));
    }

    /**
     * 获取订单详情
     */
    public function testGetList()
    {
        $client = $this->mockApiClient(Client::class);

        $data = [
            'page' => 1,
            'page_size' => 100,
            'sort_order' => 1
        ];

        $client->expects()->httpPostJson('shop/order/get_list', $data)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getList($data));
    }

    /**
     * 同步订单支付结果
     */
    public function testPay()
    {
        $client = $this->mockApiClient(Client::class);

        $outOrderId = '456abc';

        $client->expects()->httpPostJson('shop/order/pay', [
            'out_order_id' => $outOrderId,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->syncPayState([
            'out_order_id' => $outOrderId
        ]));
    }

    /**
     * 同步订单支付结果
     */
    public function testSyncPayState()
    {
        $client = $this->mockApiClient(Client::class);

        $outOrderId = '456abc';

        $client->expects()->httpPostJson('shop/order/pay', [
            'out_order_id' => $outOrderId,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->syncPayState([
            'out_order_id' => $outOrderId
        ]));
    }

    /**
     * 获取订单支付信息
     */
    public function testGetPayInfo()
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'order_id' => '456abc',
            'out_order_id' => '20220110220332',
            'openid' => 'abcdefghijklmn123',

        ];

        $client->expects()->httpPostJson('shop/order/getpaymentparams', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getPayInfo($params));
    }

    /**
     * 获取推广员订单
     */
    public function testGetFinderOrders()
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'page' => 1,
            'page_size' => 30,

        ];

        $client->expects()->httpPostJson('shop/order/get_list_by_finder', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getFinderOrders($params));
    }

    /**
     * 获取分享员订单
     */
    public function testGetSharerOrders()
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'sharer_openid' => '456abc',
            'page' => 1,
            'page_size' => 30,

        ];

        $client->expects()->httpPostJson('shop/order/get_list_by_sharer', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getSharerOrders($params));
    }
}
