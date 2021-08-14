# 微信支付

请仔细阅读并理解：[微信官方文档 - 微信支付](https://pay.weixin.qq.com/wiki/doc/apiv3/wxpay/pages/index.shtml)

## 实例化

```php
<?php
use EasyWeChat\Pay\Application;

$config = [
    'mch_id' => 1360649000,

    'private_key' => __DIR__ . '/certs/apiclient_key.pem',
    'certificate' => __DIR__ . '/certs/apiclient_cert.pem',

    /**
     * 证书序列号，可通过命令从证书获取：
     * `openssl x509 -in application_cert.pem -noout -serial`
     */
    'certificate_serial_no' => '6F2BADBE1738B07EE45C6A85C5F86EE343CAABC3',

    'http' => [
        'base_uri' => 'https://api.mch.weixin.qq.com/',
    ],

    // v2 API 秘钥
    //'secret_key' => '26db3e15cfedb44abfbb5fe94fxxxxx',
    // v3
    'secret_key' => '43A03299A3C3FED3D8CE7B820Fxxxxx',

];

$app = new Application($config);
```

## API

Application 就是一个工厂类，所有的模块都是从 `$app` 中访问，并且几乎都提供了协议和 setter 可自定义修改。

### API Client

封装了多种模式的 API 调用类，你可以选择自己喜欢的方式调用开放平台任意 API，默认自动处理了 access_token 相关的逻辑。

```php
$app->getClient(); // v3
$app->getV2Client(); // v2
```

:book: 更多说明请参阅：[API 调用](../common/client.md)

### 配置

```php
$config = $app->getConfig();
```

你可以轻松使用 `$config->get($key, $default)` 读取配置，或使用 `$config->set($key, $value)` 在调用前修改配置项。

### 支付账户

支付账户类，提供一系列 API 获取支付的基本信息：

```php
$account = $app->getMerchant();

$account->getMerchantId();
$account->getPrivateKey();
$account->getCertificate();
$account->getCertificateSerialNumber();
$account->getSecretKey();
```

## 请求示例

### Native 下单

```php
 $data = [
    'mchid' => (string)$app->getMerchant()->getMerchantId(),
    'out_trade_no' => 'native20210720xxx',
    'appid' => 'wxe2fb06xxxxxxxxxx6',
    'description' => 'Image形象店-深圳腾大-QQ公仔',
    'notify_url' => 'https://weixin.qq.com/',
    'amount' => [
        'total' => 1,
        'currency' => 'CNY',
    ]
];

$response = $app->getClient()->post('pay/transactions/native', [ 'json' => $data ]);

print_r($response->toArray());
```

### 查询订单(商户订单号)

```php

$outTradeNo = 'native20210720xxx';
$response = $app->getClient()->get("pay/transactions/out-trade-no/{$outTradeNo}", [
    'query'=>[
        'mchid' =>  $app->getMerchant()->getMerchantId()
    ]
]);

print_r($response->toArray());
```

### 查询订单(微信支付订单号)

```php

$transactionId = '217752501201407033233368018';
$response = $app->getClient()->get("pay/transactions/id/{$transactionId}", [
    'query'=>[
        'mchid' =>  $app->getMerchant()->getMerchantId()
    ]
]);

print_r($response->toArray());
```
