# 公众号

> 🚨 使用前建议熟读 [微信官方文档: 公众号](https://developers.weixin.qq.com/doc/offiaccount/Getting_Started/Overview.html)

常用的配置参数会比较少，因为除非你有特别的定制，否则基本上默认值就可以了：

```php
use EasyWeChat\OfficialAccount\Application;

$config = [
    'app_id' => 'wx3cf0f39249eb0exx',
    'secret' => 'f1c242f4f28f735d4687abb469072axx',
    'token' => 'easywechat',
    'aes_key' => '', // 明文模式请勿填写 EncodingAESKey

    /**
     * OAuth 配置
     *
     * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
     * callback：OAuth授权完成后的回调页地址
     */
    'oauth' => [
        'scopes'   => ['snsapi_userinfo'],
        'callback' => '/examples/oauth_callback.php',
    ],

    /**
     * 接口请求相关配置，超时时间等，具体可用参数请参考：
     * https://github.com/symfony/symfony/blob/5.3/src/Symfony/Contracts/HttpClient/HttpClientInterface.php
     */
    'http' => [
        'timeout' => 5.0,
        // 'base_uri' => 'https://api.weixin.qq.com/', // 如果你在国外想要覆盖默认的 url 的时候才使用，根据不同的模块配置不同的 uri

        'retry' => true, // 使用默认重试配置
        //  'retry' => [
        //      // 仅以下状态码重试
        //      'http_codes' => [429, 500]
        //       // 最大重试次数
        //      'max_retries' => 3,
        //      // 请求间隔 (毫秒)
        //      'delay' => 1000,
        //      // 如果设置，每次重试的等待时间都会增加这个系数
        //      // (例如. 首次:1000ms; 第二次: 3 * 1000ms; etc.)
        //      'multiplier' => 3
        //  ],
    ],
];

$app = new Application($config);
```

:book: 更多配置项请参考：[配置](config.md)

## API

Application 就是一个工厂类，所有的模块都是从 `$app` 中访问，并且几乎都提供了协议和 setter 可自定义修改。

### 服务端

服务端模块封装了服务端相关的便捷操作，隐藏了部分复杂的细节，基于中间件模式可以更方便的处理消息推送和服务端验证。

```php
$app->getServer();
```

:book: 更多说明请参阅：[服务端使用文档](server.md)

### API Client

封装了多种模式的 API 调用类，你可以选择自己喜欢的方式调用公众号任意 API，默认自动处理了 access_token 相关的逻辑。

```php
$app->getClient();
```

:book: 更多说明请参阅：[API 调用](../client.md)

### 配置

```php
$config = $app->getConfig();
```

你可以轻松使用 `$config->get($key, $default)` 读取配置，或使用 `$config->set($key, $value)` 在调用前修改配置项。

### AccessToken

access_token 是公众号 API 调用的必备条件，如果你想获取它的值，你可以通过以下方式拿到当前的 access_token：

```php
$accessToken = $app->getAccessToken();
$accessToken->getToken(); // string
```

当然你也可以使用自己的 AccessToken 类：

```php
$accessToken = new MyCustomAccessToken();
$app->setAccessToken($accessToken)
```

### 网页授权

```php
$oauth = $app->getOAuth();
```

:book: 详情请参考：[网页授权](../oauth.md)

### 公众号账户

公众号账号类，提供一系列 API 获取公众号的基本信息：

```php
$account = $app->getAccount();

$account->getAppId();
$account->getSecret();
$account->getToken();
$account->getAesKey();
```
