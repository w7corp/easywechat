<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MiniProgram\Shipping;

use EasyWeChat\MiniProgram\Shipping\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testUploadShippingInfo()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        $data = [
            'order_key' => [
                'order_number_type' => 1,
                'transaction_id' => '',
                'mchid' => '',
                'out_trade_no' => ''
            ],
            'logistics_type' => 1,
            'delivery_mode' => 1,
            'is_all_delivered' => true,
            'shipping_list' => [
                'tracking_no' => '323244567777',
                'express_company' => 'DHL',
                'item_desc' => '微信红包抱枕*1个',
                'contact' => [
                    'consignor_contact' => '189****1234, 021-****1234',
                    'receiver_contact' => '189****1234'
                ],
            ],
            'upload_time' => '2022-12-15T13:29:35.120+08:00',
            'payer' => [
                'openid' => 'oUpF8uMuAJO_M2pxb1Q9zNjWeS6o'
            ]
        ];

        $client->expects()->httpPostJson('wxa/sec/order/upload_shipping_info', compact('data'))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->uploadShippingInfo($data));
    }

    public function uploadCombineShippingInfo()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        $data = [
            'order_key' => [
                'order_number_type' => 1,
                'transaction_id' => '',
                'mchid' => '',
                'out_trade_no' => ''
            ],
            'sub_orders' => [
                'order_key' => [
                    'order_number_type' => 1,
                    'transaction_id' => '',
                    'mchid' => '',
                    'out_trade_no' => ''
                ],
                'logistics_type' => 1,
                'delivery_mode' => 1,
                'is_all_delivered' => true,
                'shipping_list' => [
                    'tracking_no' => '323244567777',
                    'express_company' => 'DHL',
                    'item_desc' => '微信红包抱枕*1个',
                    'contact' => [
                        'consignor_contact' => '189****1234, 021-****1234',
                        'receiver_contact' => '189****1234'
                    ],
                ],
            ],
            'upload_time' => '2022-12-15T13:29:35.120+08:00',
            'payer' => [
                'openid' => 'oUpF8uMuAJO_M2pxb1Q9zNjWeS6o'
            ]
        ];

        $client->expects()->httpPostJson('wxa/sec/order/upload_combined_shipping_info', compact('data'))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->uploadShippingInfo($data));
    }

    public function testGetOrder()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        $data = [
            'transaction_id' => '42000020212023112332159214xx',
            'merchant_id' => '',
            'sub_merchant_id' => '',
            'merchant_trade_no' => '',
        ];

        $client->expects()->httpPostJson('wxa/sec/order/get_order', compact('data'))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getOrder($data));
    }

    public function testGetOrderList()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        $data = [
            'pay_time_range' => [
                'begin_time' => 123456789,
                'end_time' => 123456789
            ],
            'order_state' => 1,
            'openid' => '',
            'last_index' => '',
            'page_size' => 100
        ];

        $client->expects()->httpPostJson('wxa/sec/order/get_order_list', compact('data'))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getOrderList($data));
    }

    public function testNotifyConfirmReceive()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        $data = [
            'transaction_id' => '42000020212023112332159214xx',
            'merchant_id' => '',
            'sub_merchant_id' => '',
            'merchant_trade_no' => '',
            'received_time' => ''
        ];

        $client->expects()->httpPostJson('wxa/sec/order/notify_confirm_receive', compact('data'))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->notifyConfirmReceive($data));
    }

    public function testSetMsgJumpPath()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        $data = [
            'path' => 'pages/not-found',
        ];

        $client->expects()->httpPostJson('wxa/sec/order/set_msg_jump_path', compact('data'))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->setMsgJumpPath($data));
    }

    public function testIsTradeManaged()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        $data = [
            'appid' => '',
        ];

        $client->expects()->httpPostJson('wxa/sec/order/is_trade_managed', $data)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->isTradeManaged($data));
    }

    public function testIsTradeCompleted()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        $data = [
            'appid' => '',
        ];

        $client->expects()->httpPostJson('wxa/sec/order/is_trade_management_confirmation_completed', $data)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->isTradeCompleted($data));
    }
}
