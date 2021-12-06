<?php

namespace EasyWeChat\tests\MiniProgram\Transactions;

use EasyWeChat\Foundation\Application;
use EasyWeChat\Tests\TestCase;

class AfterSaleTest extends TestCase
{

    public function getApplication($queries = [], $content = null)
    {
        return new Application([]);
    }
    public function testAddAfterSale(){
        $app = $this->getApplication();
        //请参考微信官方文档 https://developers.weixin.qq.com/miniprogram/dev/platform-capabilities/business-capabilities/ministore/minishopopencomponent2/API/aftersale/add.html
        $params = [
            "out_order_id" => '',
            "out_aftersale_id" => '',
            "openid" => '',
            "type" => '',
            "create_time" => '',
            "status" => '',
            "finish_all_aftersale" => '',
            "path" => '',
            "product_infos" => '',
        ];
        $result = $app->mini_program->aftersale->add($params);
        $this->assertEquals(0, $result['errcode']);
    }
    public function testUpdateAfterSale(){
        $app = $this->getApplication();
        //请参考微信官方文档 https://developers.weixin.qq.com/miniprogram/dev/platform-capabilities/business-capabilities/ministore/minishopopencomponent2/API/aftersale/update.html
        $params = [
            "order_id" => '',
            "out_order_id" => '',
            "out_aftersale_id" => '',
            "status" => '',
            "finish_all_aftersale" => '',
        ];
        $result = $app->mini_program->aftersale->update($params);
        $this->assertEquals(0, $result['errcode']);
    }
}