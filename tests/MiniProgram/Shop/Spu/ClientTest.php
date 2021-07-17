<?php

namespace EasyWeChat\Tests\MiniProgram\Shop\Spu;

use EasyWeChat\MiniProgram\Shop\Spu\Client;
use EasyWeChat\Tests\TestCase;

/**
 * 自定义版交易组件开放接口
 *    申请接入接口
 *
 * Class ClientTest
 * @package EasyWeChat\Tests\MiniProgram\Shop\Register
 */
class ClientTest extends TestCase
{
    /**
     * 添加商品
     */
    public function testAdd()
    {
        $client = $this->mockApiClient(Client::class);

        $outProductId = 'abc123';

        $client->expects()->httpPostJson('shop/spu/add', [
            'out_product_id' => $outProductId
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->add(['out_product_id' => $outProductId]));
    }

    /**
     * 删除商品
     */
    public function testDel()
    {
        $client = $this->mockApiClient(Client::class);

        $outProductId = 'abc123';

        $client->expects()->httpPostJson('shop/spu/del', [
            'out_product_id' => $outProductId
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->del(['out_product_id' => $outProductId]));
    }

    /**
     * 获取商品
     */
    public function testGet()
    {
        $client = $this->mockApiClient(Client::class);

        $outProductId = 'abc123';

        $client->expects()->httpPostJson('shop/spu/get', [
            'out_product_id' => $outProductId
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->get(['out_product_id' => $outProductId]));
    }

    public function testGetList()
    {
        $client = $this->mockApiClient(Client::class);
        // 商品信息
        $product = [
            'status' => 5,
            'need_edit_spu' => 0,
        ];
        // 分页信息
        $page = [
            'page' => 1,
            'page_size' => 10,
        ];

        $client->expects()->httpPostJson('shop/spu/get_list', array_merge($product, $page))
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getList($product, $page));
    }

    /**
     * 撤回商品审核
     */
    public function testDelAudit()
    {
        $client = $this->mockApiClient(Client::class);

        $outProductId = 'abc123';

        $client->expects()->httpPostJson('shop/spu/del_audit', [
            'out_product_id' => $outProductId
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->delAudit(['out_product_id' => $outProductId]));
    }

    /**
     * 更新商品
     */
    public function testUpdate()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('shop/spu/update', [])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->update([]));
    }

    /**
     * 该免审更新商品
     */
    public function testUpdateWithoutAudit()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('shop/spu/update_without_audit', [])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->updateWithoutAudit([]));
    }

    /**
     * 上架商品
     */
    public function testListing()
    {
        $client = $this->mockApiClient(Client::class);

        $outProductId = 'abc123';

        $client->expects()->httpPostJson('shop/spu/listing', [
            'out_product_id' => $outProductId
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->listing([
            'out_product_id' => $outProductId
        ]));
    }

    /**
     * 下架商品
     */
    public function testDelisting()
    {
        $client = $this->mockApiClient(Client::class);

        $outProductId = 'abc123';

        $client->expects()->httpPostJson('shop/spu/delisting', [
            'out_product_id' => $outProductId
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->delisting([
            'out_product_id' => $outProductId
        ]));
    }
}
