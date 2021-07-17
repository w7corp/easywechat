<?php

namespace EasyWeChat\Tests\MiniProgram\Shop\Delivery;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\MiniProgram\Shop\Delivery\Client;

/**
 * 自定义版交易组件开放接口
 *    物流接口
 *
 * @package EasyWeChat\Tests\MiniProgram\Shop\Delivery
 * @author HaoLiang <haoliang@qiyuankeji.cn>
 */
class ClientTest extends TestCase
{
    /**
     * 获取快递公司列表
     */
    public function testGetCompanyList()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('shop/delivery/get_company_list')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getCompanyList());
    }

    /**
     * 订单发货
     */
    public function testSend()
    {
        $client = $this->mockApiClient(Client::class);

        $outProductId = 'abc123';

        $client->expects()->httpPostJson('shop/delivery/send', [
            'out_order_id' => $outProductId
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->send([
            'out_order_id' => $outProductId
        ]));
    }

    /**
     * 订单确认收货
     */
    public function testRecieve()
    {
        $client = $this->mockApiClient(Client::class);

        $outProductId = 'abc123';

        $openid = 'abcdefj12345';

        $client->expects()->httpPostJson('shop/delivery/recieve', [
            'out_order_id' => $outProductId,
            'openid' => $openid,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->recieve([
            'out_order_id' => $outProductId,
            'openid' => $openid,
        ]));
    }
}
