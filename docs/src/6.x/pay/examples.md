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

<details>
   <summary>付款（V2）</summary>

```php
$params = [
  'mch_appid' => $app->getConfig()['app_id'], //注意在配置文件中加上app_id
  'mchid' => $app->getConfig()['mch_id'], //商户号
  'partner_trade_no' => '202203081646729819743', // 商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
  'openid' => 'ogn1H45HCRxVRiEMLbLLuABbxxxx', //用户openid
  'check_name' => 'FORCE_CHECK',// NO_CHECK：不校验真实姓名, FORCE_CHECK：强校验真实姓名
  're_user_name'=> '彭旭', // 如果 check_name 设置为FORCE_CHECK，则必填用户真实姓名
  'amount' => 100, //金额
  'desc' => '理赔', // 企业付款操作说明信息。必填
  ];
  
$params = (new \EasyWeChat\Pay\LegacySignature($app->getMerchant()))->sign($params);

$response = $api->post('/mmpaymkttransfers/promotion/transfers', [
  'body' => \EasyWeChat\Kernel\Support\Xml::build($params), //参数xml
  'local_cert' => $app->getConfig()['certificate'], //证书
  'local_pk' => $app->getConfig()['private_key'], //证书密钥
]);

print_r(\EasyWeChat\Kernel\Support\Xml::parse($response->getContent()));
```
</details>
  
  
<!--
<details>
    <summary>标题</summary>
内容
</details>
-->
