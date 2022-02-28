# 企业微信

请仔细阅读并理解：[企业微信 API - 企业内部开发](https://open.work.weixin.qq.com/api/doc/90000/90135/90664)

## 实例化

```php
<?php
use EasyWeChat\Work\Application;

$config = [
  'corp_id' => 'wx3cf0f39249eb0exx',
  'secret' => 'f1c242f4f28f735d4687abb469072axx',
  'token' => 'easywechat',
  'aes_key' => '35d4687abb469072a29f1c242xxxxxx', 
];

$app = new Application($config);
```

## API

Application 就是一个工厂类，所有的模块都是从 `$app` 中访问，并且几乎都提供了协议和 setter 可自定义修改。

### 服务端

服务端模块封装了服务端相关的便捷操作，隐藏了部分复杂的细节，基于中间件模式可以更方便的处理消息推送和服务端验证。

```php
$app->getServer();
```

:book: 更多说明请参阅：[服务端使用文档](server.md)

### API Client

封装了多种模式的 API 调用类，你可以选择自己喜欢的方式调用开放平台任意 API，默认自动处理了 access_token 相关的逻辑。

```php
$app->getClient();
```

:book: 更多说明请参阅：[API 调用](../client.md)

### 配置

```php
$config = $app->getConfig();
```

你可以轻松使用 `$config->get($key, $default)` 读取配置，或使用 `$config->set($key, $value)` 在调用前修改配置项。

### getAccessToken

access_token 是 API 调用的必备条件，如果你想获取它的值，你可以通过以下方式拿到当前的 access_token：

```php
$accessToken = $app->getAccessToken();
$accessToken->getToken(); // string
```

当然你也可以使用自己的 getAccessToken 类：

```php
$accessToken = new MyCustomAccessToken();
$app->getAccessToken($accessToken)
```

### 企业账户

企业账号类，提供一系列 API 获取企业的基本信息：

```php
$account = $app->getAccount();

$account->getCorpId();
$account->getSecret();
$account->getToken();
$account->getAesKey();
```

## 企业网页授权

> [点此查看官方文档](https://open.work.weixin.qq.com/api/doc/90000/90135/91020)

```php
$oauth = $app->getOAuth();
```

:book: 详情请参考：[网页授权](./oauth.md)
