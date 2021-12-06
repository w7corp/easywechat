<?php

namespace EasyWeChat\tests\MiniProgram\Transactions;

use EasyWeChat\Foundation\Application;
use EasyWeChat\Tests\TestCase;

class OrderTest extends TestCase
{
    public function getApplication($queries = [], $content = null)
    {
        return new Application([]);
    }

    public function testCreateOrder(){
        $app = $this->getApplication();
        //请参考微信官方文档 https://developers.weixin.qq.com/miniprogram/dev/platform-capabilities/business-capabilities/ministore/minishopopencomponent2/API/order/add_order.html
        $params = [
            "create_time" => '',
            "type" => '',
            "out_order_id" => '',
            "openid" => '',
            "path" => '',
            "out_user_id" => '',
            "order_detail" => '',
            "delivery_detail" => '',
            "address_info" => '',
        ];
        $result = $app->mini_program->order->createOrder($params);
        $this->assertEquals(0, $result['errcode']);
    }
    public function testPayOrder(){
        $app = $this->getApplication();
        //请参考微信官方文档 https://developers.weixin.qq.com/miniprogram/dev/platform-capabilities/business-capabilities/ministore/minishopopencomponent2/API/order/pay_order.html
        $params = [
            "order_id" => '',
            "out_order_id" => '',
            "openid" => '',
            "action_type" => '',
            "action_remark" => '',
            "transaction_id" => '',
            "pay_time" => '',
        ];
        $result = $app->mini_program->order->payOrder($params);
        $this->assertEquals(0, $result['errcode']);
    }

}