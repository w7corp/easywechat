# 支付

你在阅读本文之前确认你已经仔细阅读了：[微信支付 | 商户平台开发文档](https://pay.weixin.qq.com/wiki/doc/api/index.html)。

## 配置

配置在前面的例子中已经提到过了，支付的相关配置如下：

```php
use EasyWeChat\Factory;

$config = [
    // 必要配置
    'app_id'             => 'xxxx',
    'mch_id'             => 'your-mch-id',
    'key'                => 'key-for-signature',   // API v2 密钥 (注意: 是v2密钥 是v2密钥 是v2密钥)

    // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
    'cert_path'          => 'path/to/your/cert.pem', // XXX: 绝对路径！！！！
    'key_path'           => 'path/to/your/key',      // XXX: 绝对路径！！！！

    'notify_url'         => '默认的订单回调地址',     // 你也可以在下单时单独设置来想覆盖它
];

$app = Factory::payment($config);
```

### 服务商

#### 设置子商户信息

```php
$app->setSubMerchant('sub-merchant-id', 'sub-app-id');  // 子商户 AppID 为可选项
```

### 刷卡支付

[官方文档](https://pay.weixin.qq.com/wiki/doc/api/micropay.php?chapter=9_10)

```php
$result = $app->pay([
    'body' => 'image形象店-深圳腾大- QQ公仔',
    'out_trade_no' => '1217752501201407033233368018',
    'total_fee' => 888,
    'auth_code' => '120061098828009406',
]);
```

## 授权码查询 OPENID 接口

```php
$app->authCodeToOpenid($authCode);
```

## 沙箱模式

微信支付沙箱环境，是提供给微信支付商户的开发者，用于模拟支付及回调通知。以验证商户是否理解回调通知、账单格式，以及是否对异常做了正确的处理。EasyWeChat SDK 对于这一功能进行了封装，开发者只需一步即可在沙箱模式和常规模式间切换，方便开发与最终的部署。

```php
// 在实例化的时候传入配置即可
$app = Factory::payment([
    // ...
    'sandbox' => true, // 设置为 false 或注释则关闭沙箱模式
]);

// 判断当前是否为沙箱模式：
bool $app->inSandbox();
```

> 特别注意，沙箱模式对于测试用例有严格要求，若使用的用例与规定不符，将导致测试失败。具体用例要求可关注公众号“微信支付商户接入验收助手”（WXPayAssist）查看。
