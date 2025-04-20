# 服务端

支付推送和公众号几乎一样，请参考：[公众号：服务端](../official-account/server.md)。

## 官方文档

- [基础下单支付结果通知文档](https://pay.weixin.qq.com/doc/v3/merchant/4012284311)
- [合单支付结果通知文档](https://pay.weixin.qq.com/doc/v3/partner/4012237246)
- [退款结果通知文档](https://pay.weixin.qq.com/doc/v3/merchant/4012791865)

## 内置事件处理器

SDK 内置了两个便捷方法以便于开发者快速处理支付推送事件：

> `$message` 属性已经默认解密，可直接访问解密后的属性；
> 
> 成功状态 SDK 默认会返回 success, 你可以不用返回任何东西；

### 支付成功事件

🚨 切记：推送信息不一定靠谱，可能是伪造的，所以拿到推送通知，只取订单号等必要信息，其它信息忽略，拿订单号重新查询微信支付订单的最新状态再做处理。

> :book: [官方文档：支付结果通知](https://pay.weixin.qq.com/doc/v3/merchant/4012284311)

```php
$server = $app->getServer();
$server->handlePaid(function (Message $message, \Closure $next) {
    // $message->out_trade_no 获取商户订单号
    // $message->payer['openid'] 获取支付者 openid
    // 🚨🚨🚨 注意：推送信息不一定靠谱哈，请务必验证
    // 建议是拿订单号调用微信支付查询接口，以查询到的订单状态为准
    return $next($message);
});

// 默认返回 ['code' => 'SUCCESS', 'message' => '成功']
return $server->serve();
```

### 退款成功事件

> :book: [官方文档：退款结果通知](https://pay.weixin.qq.com/doc/v3/merchant/4012791865)

```php
$server = $app->getServer();
$server->handleRefunded(function (Message $message, \Closure $next) {
    // $message->out_trade_no 获取商户订单号
    // $message->payer['openid'] 获取支付者 openid
    return $next($message);
});

// 默认返回 ['code' => 'SUCCESS', 'message' => '成功']
return $server->serve();
```

🚨 注意：经网友发现官方仍存在[使用 v2 模式的退款推送](https://pay.weixin.qq.com/doc/v2/merchant/4011985425)，所以如果你的退款逻辑有异常，请参考以下方式实现（需要配置 v2 API key）：

> 网友反馈的[问题：#2737](https://github.com/w7corp/easywechat/issues/2737)
> 目前已知的情况是：微信商户平台填写的 API 回调地址，然后在商户平台手动处理退款的。

```php
// 建议使用单独的路由处理退款！
$server = $app->getServer();

// 推送消息，已解密
// 结构参考：https://pay.weixin.qq.com/doc/v2/merchant/4011985425
$message = $server->getReqeustMessage();

// 你的逻辑...

// 返回 SUCCESS 或者 FAIL 等其他状态
return new \Nyholm\Psr7\Response(
        200, [],
      \EasyWeChat\Kernel\Support\Xml::build([
        'return_code' => 'SUCCESS',
        'return_msg' => 'OK'
      ])
);
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

`$message` 为一个 `EasyWeChat\Pay\Message` 实例。

你可以在处理完逻辑后自行创建一个响应，当然，在不同的框架里，响应写法也不一样，请自行实现。


## 回调消息

微信推送的回调消息是默认密文的，可参考[文档](https://pay.weixin.qq.com/doc/v3/merchant/4012071382)，但是 SDK 已经帮你解密好了，所以以上例子中的 `$message` 默认访问的属性都是明文的，例如：

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
