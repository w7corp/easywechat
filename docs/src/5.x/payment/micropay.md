# 付款码支付

## 配置

> 请务必先熟悉流程：<https://pay.weixin.qq.com/wiki/doc/api/micropay.php?chapter=5_1>


```php
$result = $app->pay([
    'body' => 'image形象店-深圳腾大- QQ公仔',
    'out_trade_no' => '20150806125346',
    'total_fee' => 88,
    'spbill_create_ip' => '123.12.12.123', // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
    'auth_code' => '120061098828009406', // 扫码支付付款码，设备读取用户微信中的条码或者二维码信息
]);
```

#### 支付结果

付款码支付方式没有回调通知，支付结果直接返回，请参考：[微信付款码支付文档](https://pay.weixin.qq.com/wiki/doc/api/micropay.php?chapter=5_1) 更新您的订单状态。
