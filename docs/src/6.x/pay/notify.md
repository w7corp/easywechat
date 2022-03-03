# 微信支付的回调通知

## 微信官方文档

- [基础下单支付结果通知文档](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_5.shtml)
- [合单支付结果通知文档](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter5_1_13.shtml)
- [退款结果通知文档](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_11.shtml)

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

> 记得需要将此类路由关闭 csrf 验证。

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

#### 回调消息

微信推送的回调消息是默认密文的，可[参考文档](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_5.shtml)，但是 SDK 已经帮你解密好了，所以以上例子中的 `$message` 默认访问的属性都是明文的，例如：

```json
{
    "transaction_id":"1217752501201407033233368018",
    "amount":{
        "payer_total":100,
        "total":100,
        "currency":"CNY",
        "payer_currency":"CNY"
    },
    "mchid":"1230000109",
    "trade_state":"SUCCESS",
    "bank_type":"CMC",
    "promotion_detail":[...],
    "success_time":"2018-06-08T10:34:56+08:00",
    "payer":{
        "openid":"oUpF8uMuAJO_M2pxb1Q9zNjWeS6o"
    },
    "out_trade_no":"1217752501201407033233368018",
    "appid":"wxd678efh567hg6787",
    "trade_state_desc":"支付成功",
    "trade_type":"MICROPAY",
    "attach":"自定义数据",
    "scene_info":{
        "device_id":"013467007045764"
    }
}
```

所以你可以直接使用 `$message->transaction_id` 或者 `$message['transaction_id']` 来访问以上属性。

#### 怎么获取密文属性呢？

`$message` 对象提供了 `$message->getOriginalAttributes()` 来获取加密前的数据：

```json
{
    "id": "EV-2018022511223320873",
    "create_time": "2015-05-20T13:29:35+08:00",
    "resource_type": "encrypt-resource",
    "event_type": "TRANSACTION.SUCCESS",
    "summary": "支付成功",
    "resource": {
        "original_type": "transaction",
        "algorithm": "AEAD_AES_256_GCM",
        "ciphertext": "",
        "associated_data": "",
        "nonce": ""
    }
}
```

当然我们还特别封装了用于获取事件类型的方法：

```php
$message->getEventType(); // TRANSACTION.SUCCESS
```
