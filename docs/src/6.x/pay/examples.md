# 示例

<details>
    <summary>JSAPI 下单</summary>

> 官方文档：<https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_1.shtml>

```php
$response = $app->getClient()->post("v3/pay/transactions/jsapi", [
   "mchid" => "1518700000", // <---- 请修改为您的商户号
   "out_trade_no" => "native12177525012012070352333'.rand(1,1000).'",
   "appid" => "wx6222e9f48a0xxxxx", // <---- 请修改为服务号的 appid
   "description" => "Image形象店-深圳腾大-QQ公仔",
   "notify_url" => "https://weixin.qq.com/",
   "amount" => [
        "total" => 1,
        "currency" => "CNY"
    ],
    "payer" => [
        "openid" => "o4GgauInH_RCEdvrrNGrnxxxxxx" // <---- 请修改为服务号下单用户的 openid
    ]
]);

\dd($response->toArray(false));
```

</details>


<details>
    <summary>Native 下单</summary>

```php
$response = $app->getClient()->post('pay/transactions/native', [
    'mchid' => (string)$app->getMerchant()->getMerchantId(),
    'out_trade_no' => 'native20210720xxx',
    'appid' => 'wxe2fb06xxxxxxxxxx6',
    'description' => 'Image形象店-深圳腾大-QQ公仔',
    'notify_url' => 'https://weixin.qq.com/',
    'amount' => [
        'total' => 1,
        'currency' => 'CNY',
    ]
]);

print_r($response->toArray());
```
</details>


<details>
    <summary>查询订单（商户订单号）</summary>

```php

$outTradeNo = 'native20210720xxx';
$response = $app->getClient()->get("pay/transactions/out-trade-no/{$outTradeNo}", [
    'query'=>[
        'mchid' =>  $app->getMerchant()->getMerchantId()
    ]
]);

print_r($response->toArray());
```
</details>


<details>
    <summary>查询订单（微信订单号）</summary>

```php
$transactionId = '217752501201407033233368018';
$response = $app->getClient()->get("pay/transactions/id/{$transactionId}", [
    'query'=>[
        'mchid' =>  $app->getMerchant()->getMerchantId()
    ]
]);

print_r($response->toArray());
```
</details>


<!--
<details>
    <summary>标题</summary>
内容
</details>
-->
