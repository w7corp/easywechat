<?php

namespace EasyWeChat\Tests\MiniProgram\Shop\Aftersale;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\MiniProgram\Shop\Aftersale\Client;

/**
 * 自定义版交易组件开放接口
 *    售后接口
 *
 * @package EasyWeChat\Tests\MiniProgram\Shop\Basic
 * @author HaoLiang <haoliang@qiyuankeji.cn>
 */
class ClientTest extends TestCase
{
    /**
     * 创建售后
     */
    public function testAdd()
    {
        $client = $this->mockApiClient(Client::class);

        $outOrderId = '456abc';

        $client->expects()->httpPostJson('shop/ecaftersale/add', [
            'out_order_id' => $outOrderId
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->add([
            'out_order_id' => $outOrderId
        ]));
    }

    /**
     * 获取订单下售后单
     */
    public function testGet()
    {
        $client = $this->mockApiClient(Client::class);

        $outOrderId = '456abc';

        $client->expects()->httpPostJson('shop/ecaftersale/get', [
            'out_order_id' => $outOrderId
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->get([
            'out_order_id' => $outOrderId
        ]));
    }

    /**
     * 更新售后
     */
    public function testUpdate()
    {
        $client = $this->mockApiClient(Client::class);

        $outOrderId = '456abc';

        $client->expects()->httpPostJson('shop/ecaftersale/update', [
            'out_order_id' => $outOrderId,
            'out_aftersale_id' => 1,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->update(
            ['out_order_id' => $outOrderId],
            ['out_aftersale_id' => 1]
        ));
    }
}
