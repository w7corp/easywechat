# 微信支付的回调通知

## 微信官方文档
- 基础下单支付结果通知文档 https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_5.shtml
- 合单支付结果通知文档 https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter5_1_13.shtml
- 退款结果通知文档 https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_11.shtml

## 通知处理器

- 支付结果通知 `handlePaid`
- 退款结果通知 `handleRefunded`

```php
$server = $app->getServer();

// 处理支付结果事件
$server->handlePaid(callable | string $handler);

// 处理退款结果事件
$server->handleRefunded(callable | string $handler);

return $server->serve();
```

### 示例（Laravel 框架）

> 类路由关闭 csrf 验证。

```php
// 假设你设置的通知地址notify_url为: https://easywechat.com/payment_notify 

// 注意：通知地址notify_url必须为https协议

Route::post('payment_notify', function () {
    // $app 为你实例化的支付对象，此处省略实例化步骤
    $server = $app->getServer();

    // 处理支付结果事件
    $server->handlePaid(function ($message) {
        // $message 为微信推送的通知结果，详看微信官方文档 

        // 微信支付订单号 $message['transaction_id']
        // 商户订单号 $message['out_trade_no']
        // 商户号 $message['mchid']
        // 具体看微信官方文档...
        // 进行业务处理，如存数据库等...
    });

    // 处理退款结果事件
    $server->handleRefunded(function ($message) {
        // 同上，$message 详看微信官方文档 
        // 进行业务处理，如存数据库等...
    });

    return $server->serve();
});
```
