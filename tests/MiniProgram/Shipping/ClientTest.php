<?php

namespace EasyWeChat\Tests\MiniProgram\Shipping;

use EasyWeChat\MiniProgram\Shipping\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testUploadShippingInfo()
    {
        $client = $this->mockApiClient(Client::class);

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

        $client->expects()->httpPostJson('wxa/sec/order/upload_shipping_info', $data)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->uploadShippingInfo($data));

    }

    public function testUploadCombineShippingInfo()
    {
        $client = $this->mockApiClient(Client::class);

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

        $client->expects()->httpPostJson('wxa/sec/order/upload_combined_shipping_info', $data)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->uploadCombineShippingInfo($data));
    }

    public function testGetOrder()
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'transaction_id' => '42000020212023112332159214'
        ];

        $client->expects()->httpPostJson('wxa/sec/order/get_order', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getOrder($params));
    }

    public function testGetOrderList()
    {
        $client = $this->mockApiClient(Client::class);

        $data = [];

        $client->expects()->httpPostJson('wxa/sec/order/get_order_list', $data)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getOrderList($data));
    }

    public function testNotifyConfirmReceive()
    {
        $client = $this->mockApiClient(Client::class);

        $data = [
            'transaction_id' => '42000020212023112332159214xx',
            'received_time' => ''
        ];

        $client->expects()->httpPostJson('wxa/sec/order/notify_confirm_receive', $data)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->notifyConfirmReceive($data));
    }

    public function testSetMsgJumpPath()
    {
        $client = $this->mockApiClient(Client::class);

        $data = [
            'path' => 'pages/not-found',
        ];

        $client->expects()->httpPostJson('wxa/sec/order/set_msg_jump_path', $data)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->setMsgJumpPath('pages/not-found'));
    }

    public function testIsTradeManaged()
    {
        $client = $this->mockApiClient(Client::class);

        $data = [
            'appid' => '123',
        ];

        $client->expects()->httpPostJson('wxa/sec/order/is_trade_managed', $data)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->isTradeManaged('123'));
    }

    public function testIsTradeCompleted()
    {
        $client = $this->mockApiClient(Client::class);

        $data = [
            'appid' => '123',
        ];

        $client->expects()->httpPostJson('wxa/sec/order/is_trade_management_confirmation_completed', $data)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->isTradeCompleted('123'));
    }
}
