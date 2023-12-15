# 小程序发货信息管理

> 微信文档 https://developers.weixin.qq.com/miniprogram/dev/platform-capabilities/business-capabilities/order-shipping/order-shipping.html


## 发货信息录入接口

```php 
$data = [
    'order_key' => [
        'order_number_type' => 1,
        'mchid' => '',
        'out_trade_no' => ''
    ],
    'logistics_type' => 4,
    'delivery_mode' => 1,
    'shipping_list' => [
        'tracking_no' => '323244567777',
        'express_company' => 'DHL',
        'item_desc' => '微信红包抱枕*1个',
        'contact' => [
            'consignor_contact' => '189****1234',
            'receiver_contact' => '189****1234'
        ],
    ],
    'upload_time' => '2022-12-15T13:29:35.120+08:00',
    'payer' => [
        'openid' => 'oUpF8uMuAJO_M2pxb1Q9zNjWeS6o'
    ]
];

$app->shipping->uploadShippingInfo($data);
```

## 发货信息合单录入接口

```php
 $data = [
    'order_key' => [
        'order_number_type' => 1,
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
        'logistics_type' => 4,
        'delivery_mode' => 1,
        'shipping_list' => [
            'tracking_no' => '323244567777',
            'express_company' => 'DHL',
            'item_desc' => '微信红包抱枕*1个',
            'contact' => [
                'consignor_contact' => '189****1234',
                'receiver_contact' => '189****1234'
            ],
        ],
    ],
    'upload_time' => '2022-12-15T13:29:35.120+08:00',
    'payer' => [
        'openid' => 'oUpF8uMuAJO_M2pxb1Q9zNjWeS6o'
    ]
];

$app->shipping->uploadCombineShippingInfo($data);
```

## 查询订单发货状态

```php
$data = $app->shipping->getOrder([
    'transaction_id' => 'xxx'
]);
```

## 查询订单列表

```php
$data = $app->shipping->getOrderList();
```

## 确认收货提醒接口

```php
$data = [
    'transaction_id' => '42000020212023112332159214xx',
    'received_time' => ''
];

$app->shipping->notifyConfirmReceive($data);
```

## 消息跳转路径设置接口

```php
$data = [
    'path' => 'pages/goods/order_detail?id=xxxx',
];

$app->shipping->setMsgJumpPath($data);
```

## 查询小程序是否已开通发货信息管理服务

```php
$app->shipping->isTradeManaged();
```

## 查询小程序是否已完成交易结算管理确认

```php
$app->shipping->isTradeCompleted();
```
