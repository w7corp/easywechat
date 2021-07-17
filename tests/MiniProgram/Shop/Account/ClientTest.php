<?php

namespace EasyWeChat\Tests\MiniProgram\Shop\Account;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\MiniProgram\Shop\Account\Client;

/**
 * 自定义版交易组件开放接口
 *    商家入驻接口
 *
 * @package EasyWeChat\Tests\MiniProgram\Shop\Basic
 * @author HaoLiang <haoliang@qiyuankeji.cn>
 */
class ClientTest extends TestCase
{
    /**
     * 获取商家类目列表
     */
    public function testGetCategoryList()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('shop/account/get_category_list')
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getCategoryList());
    }

    /**
     * 获取商家品牌列表
     */
    public function testGetBrandList()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('shop/account/get_brand_list')
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getBrandList());
    }

    /**
     * 更新商家信息
     */
    public function testUpdateInfo()
    {
        $client = $this->mockApiClient(Client::class);
        $path = 'pages/home/index';
        $phone = '13904118888';

        $client->expects()->httpPostJson('shop/account/update_info', [
            'service_agent_path' => $path,
            'service_agent_phone' => $phone,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->updateInfo($path, $phone));
    }

    /**
     * 获取商家信息
     */
    public function testGetInfo()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('shop/account/get_info')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getInfo());
    }
}
