# 示例

<details>
    <summary>JSAPI 下单</summary>

> 官方文档：<https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_1.shtml>

```php
$config = [
    'mch_id' => 1518700000,

    'private_key' => __DIR__ . '/certs/apiclient_key.pem',
    'certificate' => __DIR__ . '/certs/apiclient_cert.pem',

    /**
     * 证书序列号，可通过命令从证书获取：
     * `openssl x509 -in apiclient_cert.pem -noout -serial`
     */
    'certificate_serial_no' => '69701D37B35989A9195D21E9C8xxxxxxxx',

    'http' => [
        'base_uri' => 'https://api.mch.weixin.qq.com/',
    ],

    // v3
    'secret_key' => 'Sx7cSGLXszB9I1iKJvgDNzNxxxxx',
];

$app = new \EasyWeChat\Pay\Application($config);

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
