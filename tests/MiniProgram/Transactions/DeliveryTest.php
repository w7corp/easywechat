<?php

namespace EasyWeChat\tests\MiniProgram\Transactions;

use EasyWeChat\Foundation\Application;
use EasyWeChat\Tests\TestCase;

class DeliveryTest extends TestCase
{

    public function getApplication($queries = [], $content = null)
    {
        return new Application([]);
    }
    public function testSendDelivery(){
        $app = $this->getApplication();
        //请参考微信官方文档 https://developers.weixin.qq.com/miniprogram/dev/platform-capabilities/business-capabilities/ministore/minishopopencomponent2/API/delivery/send.html
        $params = [
            "order_id" => '',
            "out_order_id" => '',
            "openid" => '',
            "finish_all_delivery" => '',
            "delivery_list" => '',
        ];
        $result = $app->mini_program->delivery->sendDelivery($params);
        $this->assertEquals(0, $result['errcode']);
    }
}