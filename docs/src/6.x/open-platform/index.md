# 微信开放平台第三方平台

请仔细阅读并理解：[微信官方文档 - 开放平台 - 第三方平台](https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/product/Third_party_platform_appid.html)

## 实例化

```php
<?php
use EasyWeChat\OpenPlatform\Application;

$config = [
  'app_id' => 'wx3cf0f39249eb0exx',
  'secret' => 'f1c242f4f28f735d4687abb469072axx',
  'token' => 'easywechat',
  'aes_key' => '' // 明文模式请勿填写 EncodingAESKey
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

### ComponentAccessToken

access_token 是开放平台 API 调用的必备条件，如果你想获取它的值，你可以通过以下方式拿到当前的 access_token：

```php
$componentAccessToken = $app->getComponentAccessToken();
$componentAccessToken->getToken(); // string
```

当然你也可以使用自己的 ComponentAccessToken 类：

```php
$componentAccessToken = new MyCustomComponentAccessToken();
$app->setComponentAccessToken($componentAccessToken)
```

### VerifyTicket

你可以通过以下方式拿到当前 verify_ticket 类：

```php
$verifyTicket = $app->getVerfiyTicket();

$verifyTicket->getTicket(); // strval
```

### 开放平台账户

开放平台账号类，提供一系列 API 获取开放平台的基本信息：

```php
$account = $app->getAccount();

$account->getAppId();
$account->getSecret();
$account->getToken();
$account->getAesKey();
```

## 第三方应用或网站网页授权

> 注意：不是代公众号/小程序授权。

第三方应用或者网站网页授权的逻辑和公众号的网页授权基本一样：

```php
$oauth = $app->getOAuth();
```

:book: 详情请参考：[网页授权](../oauth.md)

## 使用授权码获取授权信息

在用户在授权页授权流程完成后，授权页会自动跳转进入回调 URI，并在 URL 参数中返回授权码和过期时间，如：(`https://easywechat.com/callback?auth_code=xxx&expires_in=600`)

```php
$authorizationCode = '授权成功时返回给第三方平台的授权码';

$authorization = $app->getAuthorization($authorizationCode);

$authorization->getAppId(); // authorizer_appid
$authorization->getAccessToken(); // EasyWeChat\OpenPlatform\AuthorizerAccessToken
$authorization->getRefreshToken(); // authorizer_access_token
$authorization->toArray();
$authorization->toJson();

// {
//   "authorization_info": {
//     "authorizer_appid": "wxf8b4f85f3a79...",
//     "authorizer_access_token": "QXjUqNqfYVH0yBE1iI_7vuN_9gQbpjfK7M...",
//     "expires_in": 7200,
//     "authorizer_refresh_token": "dTo-YCXPL4llX-u1W1pPpnp8Hgm4wpJt...",
//     "func_info": [
//       {
//         "funcscope_category": {
//           "id": 1
//         }
//       },
//       //...
//     ]
//   }
// }

```

## 获取/刷新接口调用令牌

在公众号/小程序接口调用令牌（`authorizer_access_token`）失效时，可以使用刷新令牌（authorizer_refresh_token）获取新的接口调用令牌。

> horizer_access_token`有效期为 2 小时，开发者需要缓存`authorizer_access_token`，避免获取/刷新接口调用令牌的 API 调用触发每日限额。缓存方法可以参考：<https://developers.weixin.qq.com/doc/offiaccount/Basic_Information/Get_access_token.html>

```php
$authorizerAppId = '授权方 appid';
$authorizerRefreshToken = '刷新令牌，上一步得到的 authorizer_refresh_token';

$app->refreshAuthorizerToken($authorizerAppId, $authorizerRefreshToken)

// {
//   "authorizer_access_token": "some-access-token",
//   "expires_in": 7200,
//   "authorizer_refresh_token": "refresh_token_value"
// }
```

---

## 代替公众号/小程序请求公众平台 API

代替公众号/小程序请求，需要首先拿到 `EasyWeChat\OpenPlatform\AuthorizerAccessToken`。

**获取 AuthorizerAccessToken**

第一种方式：开放平台永久授权码换取授权者信息

```php
$authorizationCode = '授权成功时返回给第三方平台的授权码';
$authorization = $app->getAuthorization($authorizationCode);
$authorizerAccessToken = $authorization->getAccessToken();
```

第二种方式：从数据库提取出来的授权码

```php
$authorizerAccessToken = new AuthorizerAccessToken($authorizerAppId, $token);
```

**获取公众号实例**

```php
// $officialAccount 为 EasyWeChat\OfficialAccount\Application 实例
$officialAccount = $app->getOfficialAccount($authorizerAccessToken);

// 调用公众号接口
$users = $officialAccount->getClient()->cgiBin->users->list->get()->toArray();
```

:book: 更多公众号用法请参考：[公众号](../official-account/index.md)

**获取小程序实例**

```php
// $miniApp 为 EasyWeChat\MiniApp\Application 实例
$miniApp = $app->getMiniApp($authorizerAccessToken);

// 调用小程序接口
$users = $miniApp->getClient()->cgiBin->users->list->get()->toArray();
```

:book: 更多小程序用法请参考：[小程序](../mini-app/index.md)
