# 微信支付

请仔细阅读并理解：[微信官方文档 - 微信支付](https://pay.weixin.qq.com/wiki/doc/apiv3/wxpay/pages/index.shtml)

## 实例化

```php
<?php
use EasyWeChat\Pay\Application;

$config = [
    'mch_id' => 1360649000,

    // 商户证书
    'private_key' => __DIR__ . '/certs/apiclient_key.pem',
    'certificate' => __DIR__ . '/certs/apiclient_cert.pem',

     // v3 API 秘钥
    'secret_key' => '43A03299A3C3FED3D8CE7B820Fxxxxx',

    // v2 API 秘钥
    'v2_secret_key' => '26db3e15cfedb44abfbb5fe94fxxxxx',

    // 平台证书：微信支付 APIv3 平台证书，需要使用工具下载
    // 下载工具：https://github.com/wechatpay-apiv3/CertificateDownloader
    'platform_certs' => [
        // 请使用绝对路径
        // '/path/to/wechatpay/cert.pem',
    ],

    /**
     * 接口请求相关配置，超时时间等，具体可用参数请参考：
     * https://github.com/symfony/symfony/blob/5.3/src/Symfony/Contracts/HttpClient/HttpClientInterface.php
     */
    'http' => [
        'throw'  => true, // 状态码非 200、300 时是否抛出异常，默认为开启
        'timeout' => 5.0,
        // 'base_uri' => 'https://api.mch.weixin.qq.com/', // 如果你在国外想要覆盖默认的 url 的时候才使用，根据不同的模块配置不同的 uri
    ],
];

$app = new Application($config);
```

## API

Application 就是一个工厂类，所有的模块都是从 `$app` 中访问，并且几乎都提供了协议和 setter 可自定义修改。

### API Client

封装了多种模式的 API 调用类，你可以选择自己喜欢的方式调用开放平台任意 API，默认自动处理了 access_token 相关的逻辑。

```php
$app->getClient();
```

:book: 更多说明请参阅：[API 调用](../client.md)

### 工具

为了方便开发者生成各种调起支付所需配置，你可以使用工具类：

```php
$app->getUtils();
```

:book: 更多说明请参阅：[工具](utils.md)

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
$account->getSecretKey();
$account->getV2SecretKey();
$account->getPlatformCerts();
```

### 一些可能会用到的

#### 签名验证

按官方说法，建议在拿到**微信接口响应**和**接收到微信支付的回调通知**时，对通知的签名进行验证，以确保通知是微信支付发送的。

你可以通过以下方式获取签名验证器：

```php
$app->getValidator();
```

##### 推送消息的签名验证

```php
$server = $app->getServer();

$server->handlePaid(function (Message $message, \Closure $next) use ($app) {
    // $message->out_trade_no 获取商户订单号
    // $message->payer['openid'] 获取支付者 openid
    
    try{
        $app->getValidator()->validate($app->getRequest());
       // 验证通过，业务处理
    } catch(Exception $e){
      // 验证失败
    }
 
    return $next($message);
});

// 默认返回 ['code' => 'SUCCESS', 'message' => '成功']
return $server->serve();
```

##### API返回值的签名验证

```php
// API 请求示例
$response = $app->getClient()->postJson("v3/pay/transactions/jsapi", [...]);

try{
    $app->getValidator()->validate($response->toPsrResponse());
   // 验证通过
} catch(Exception $e){
  // 验证失败
}
```

#### 获取证书序列号

```bash
openssl x509 -in /path/to/merchant/apiclient_cert.pem -noout -serial | awk -F= '{print $2}'
```

