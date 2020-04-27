<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MiniProgram\Logistics;

use EasyWeChat\MiniProgram\Express\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testListProviders()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpGet('cgi-bin/express/business/delivery/getall')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->listProviders());
    }

    public function testCreateWaybill()
    {
        $client = $this->mockApiClient(Client::class);

        $data = [
            'order_id' => '01234567890123456789',
            'openid' => 'oABC123456',
            'delivery_id' => 'SF',
            'biz_id' => 'xyz',
            'custom_remark' => '易碎物品',
            'sender' => [
                'name' => '张三',
                'tel' => '18666666666',
                'mobile' => '020-88888888',
                'company' => '公司名',
                'post_code' => '123456',
                'country' => '中国',
                'province' => '广东省',
                'city' => '广州市',
                'area' => '海珠区',
                'address' => 'XX路XX号XX大厦XX栋XX',
            ],
            'receiver' => [
                'name' => '王小蒙',
                'tel' => '18610000000',
                'mobile' => '020-77777777',
                'company' => '公司名',
                'post_code' => '654321',
                'country' => '中国',
                'province' => '广东省',
                'city' => '广州市',
                'area' => '天河区',
                'address' => 'XX路XX号XX大厦XX栋XX',
            ],
            'shop' => [
                'wxa_path' => '/index/index?from=waybill&id=01234567890123456789',
                'img_url' => 'https://mmbiz.qpic.cn/mmbiz_png/OiaFLUqewuIDNQnTiaCInIG8ibdosYHhQHPbXJUrqYSNIcBL60vo4LIjlcoNG1QPkeH5GWWEB41Ny895CokeAah8A/640',
                'goods_name' => '一千零一夜钻石包&爱马仕铂金包',
                'goods_count' => 2,
            ],
            'cargo' => [
                'count' => 2,
                'weight' => 5.5,
                'space_x' => 30.5,
                'space_y' => 20,
                'space_z' => 20,
                'detail_list' => [
                    [
                        'name' => '一千零一夜钻石包',
                        'count' => 1,
                    ],
                    [
                        'name' => '爱马仕铂金包',
                        'count' => 1,
                    ],
                ],
            ],
            'insured' => [
                'use_insured' => 1,
                'insured_value' => 10000,
            ],
            'service' => [
                'service_type' => 0,
                'service_name' => '标准快递',
            ],
        ];

        $client->expects()->httpPostJson('cgi-bin/express/business/order/add', $data)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->createWaybill($data));
    }

    public function testDeleteWaybill()
    {
        $client = $this->mockApiClient(Client::class);

        $data = [
            'order_id' => '01234567890123456789',
            'openid' => 'oABC123456',
            'delivery_id' => 'SF',
            'waybill_id' => '000000000',
        ];

        $client->expects()->httpPostJson('cgi-bin/express/business/order/cancel', $data)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->deleteWaybill($data));
    }

    public function testGetWaybill()
    {
        $client = $this->mockApiClient(Client::class);

        $data = [
            'order_id' => '01234567890123456789',
            'openid' => 'oABC123456',
            'delivery_id' => 'SF',
            'waybill_id' => '000000000',
        ];

        $client->expects()->httpPostJson('cgi-bin/express/business/order/get', $data)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getWaybill($data));
    }

    public function testGetWaybillTrack()
    {
        $client = $this->mockApiClient(Client::class);

        $data = [
            'order_id' => '01234567890123456789',
            'openid' => 'oABC123456',
            'delivery_id' => 'SF',
            'waybill_id' => '000000000',
        ];

        $client->expects()->httpPostJson('cgi-bin/express/business/path/get', $data)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getWaybillTrack($data));
    }

    public function testGetBalance()
    {
        $client = $this->mockApiClient(Client::class);

        $data = [
            'delivery_id' => 'YTO',
            'biz_id' => 'xyz',
        ];

        $client->expects()->httpPostJson('cgi-bin/express/business/quota/get', $data)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getBalance('YTO', 'xyz'));
    }

    public function testGetPrinter()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/express/business/printer/getall')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getPrinter());
    }

    public function testBindPrinter()
    {
        $client = $this->mockApiClient(Client::class);

        $data = [
            'openid' => 'myopenid',
            'update_type' => 'bind',
        ];

        $client->expects()->httpPostJson('cgi-bin/express/business/printer/update', $data)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->bindPrinter('myopenid'));
    }

    public function testUnbindPrinter()
    {
        $client = $this->mockApiClient(Client::class);

        $data = [
            'openid' => 'myopenid',
            'update_type' => 'unbind',
        ];

        $client->expects()->httpPostJson('cgi-bin/express/business/printer/update', $data)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->unbindPrinter('myopenid'));
    }
}
