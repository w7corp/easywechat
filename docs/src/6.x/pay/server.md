# 服务端

支付推送和公众号几乎一样，请参考：[公众号：服务端](../official-account/server.md)。

## 官方文档

- [基础下单支付结果通知文档](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_5.shtml)
- [合单支付结果通知文档](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter5_1_13.shtml)
- [退款结果通知文档](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_11.shtml)

## 内置事件处理器

SDK 内置了两个便捷方法以便于开发者快速处理支付推送事件：

> `$message` 属性已经默认解密，可直接访问解密后的属性。

### 支付成功事件

> :book: 官方文档：支付结果通知 <https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_5.shtml>

```php
$server->handlePaid(function (Message $message, \Closure $next) {
    // $message->out_trade_no 获取商户订单号
    // $message->payer['openid'] 获取支付者 openid
    return $next($message);
});
```

### 退款成功事件

> :book: 官方文档：退款结果通知 <https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_11.shtml>

```php
$server->handleRefunded(function (Message $message, \Closure $next) {
    // $message->out_trade_no 获取商户订单号
    // $message->payer['openid'] 获取支付者 openid
    return $next($message);
});
```

## 其它事件处理

以上便捷方法都只处理了**成功状态**，其它状态，可以通过自定义事件处理中间件的形式处理：

```php
$server->with(function($message, \Closure $next) {
    // $message->event_type 事件类型
    return $next($message);
});
```

## 自助处理推送消息

你可以通过下面的方式获取来自微信服务器的推送消息：

```php
$message = $server->getRequestMessage();
```

`$message` 为一个 `EasyWeChat\OpenWork\Message` 实例。

你可以在处理完逻辑后自行创建一个响应，当然，在不同的框架里，响应写法也不一样，请自行实现。


## 回调消息

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
