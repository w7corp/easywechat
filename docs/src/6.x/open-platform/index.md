# 微信开放平台第三方平台

请仔细阅读并理解：[微信官方文档 - 开放平台 - 第三方平台](https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/product/Third_party_platform_appid.html)

## 实例化

请按如下格式配置你的开放平台账号信息，并实例化一个开放平台对象：

```php
<?php
use EasyWeChat\OpenPlatform\Application;

$config = [
  'app_id' => 'wx3cf0f39249eb0exx', // 开放平台账号的 appid
  'secret' => 'f1c242f4f28f735d4687abb469072axx',   // 开放平台账号的 secret
  'token' => 'easywechat',  // 开放平台账号的 token
  'aes_key' => ''   // 明文模式请勿填写 EncodingAESKey

  /**
   * 接口请求相关配置，超时时间等，具体可用参数请参考：
   * https://github.com/symfony/symfony/blob/5.3/src/Symfony/Contracts/HttpClient/HttpClientInterface.php
   */
  'http' => [
      'throw'  => true, // 状态码非 200、300 时是否抛出异常，默认为开启
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

> 💡 请不要把公众号/小程序的配置信息用于初始化开放平台。

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

你可以轻松使用 `$config->all()` 获取整个配置的数组。

还可以使用 `$config->get($key, $default)` 读取单个配置，或使用 `$config->set($key, $value)` 在调用前修改配置项。

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

## 创建预授权码 <version-tag>6.3.0+</version-tag>

你可以通过下面的方式创建预授权码：

```php
$reponse = $app->createPreAuthorizationCode();
// {
//   "pre_auth_code": "Cx_Dk6qiBE0Dmx4eKM-2SuzA...",
//   "expires_in": 600
// }
```

## 生成授权页地址 <version-tag>6.3.0+</version-tag>

你可以通过下面方法生成一个授权页地址，引导用户进行授权：

```php
// 自动获取预授权码模式
$url = $app->createPreAuthorizationUrl('http://easywechat.com/callback');

// 或者指定预授权码
$preAuthCode = 'createPreAuthorizationCode 得到的预授权码 pre_auth_code';
$url = $app->createPreAuthorizationUrl('http://easywechat.com/callback', $preAuthCode);
```

## 获取/刷新接口调用令牌

在公众号/小程序接口调用令牌 `authorizer_access_token` 失效时，可以使用刷新令牌 `authorizer_refresh_token` 获取新的接口调用令牌。

> `authorizer_access_token` 有效期为 2 小时，开发者需要缓存 `authorizer_access_token`，避免获取/刷新接口调用令牌的 API 调用触发每日限额。

```php
$authorizerAppId = '授权方 appid';
$authorizerRefreshToken = '上一步得到的 authorizer_refresh_token';

$app->refreshAuthorizerToken($authorizerAppId, $authorizerRefreshToken)

// {
//   "authorizer_access_token": "some-access-token",
//   "expires_in": 7200,
//   "authorizer_refresh_token": "refresh_token_value"
// }
```

---

## 代替公众号/小程序请求 API

代替公众号/小程序请求，需要首先拿到 `EasyWeChat\OpenPlatform\AuthorizerAccessToken`，用以代替公众号的 Access Token，官方流程说明：[开发前必读 /Token 生成介绍](https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/Before_Develop/creat_token.html) 。

### 获取 AuthorizerAccessToken

你可以使用开放 **平台永久授权码** 换取授权者信息，然后换取 Authorizer Access Token：

```php
$authorizationCode = '授权成功时返回给第三方平台的授权码';
$authorization = $app->getAuthorization($authorizationCode);
$authorizerAccessToken = $authorization->getAccessToken();
```

> 🚨 Authorizer Access Token 只有 2 小时有效期，不建议将它存储到数据库，当然如果你不得不这么做，请记得参考上面 「**获取/刷新接口调用令牌**」章节刷新。

### 代公众号调用

**方式一：使用 authorizer_refresh_token** <version-tag>6.3.0+</version-tag>

此方式适用于大部分场景，将授权信息存储到数据库中，代替调用时取出对应公众号的 authorizer_refresh_token 即可。

```php
$authorizerRefreshToken = '公众号授权时得到的 authorizer_refresh_token';
$officialAccount = $app->getOfficialAccountWithRefreshToken($appId, $authorizerRefreshToken);
```

**方式二：使用 authorizer_access_token** <version-tag>6.3.0+</version-tag>

此方案适用于使用独立的中央授权服务单独维护授权信息的方式。

```php
$authorizerAccessToken = '公众号授权时得到的 authorizer_access_token';
$officialAccount = $app->getOfficialAccountWithAccessToken($appId, $authorizerAccessToken);
```

**方式三：使用 AuthorizerAccessToken 类**

不推荐，请使用方式一或者二，此方法由于设计之初没有充分考虑到使用场景，导致使用很麻烦。

```php
// $token 为你存到数据库的授权码 authorizer_access_token
$authorizerAccessToken = new AuthorizerAccessToken($authorizerAppId, $token);
$officialAccount = $app->getOfficialAccount($authorizerAccessToken);


使用以上方式初始化公众号对象后，可以直接调用公众号的 API 方法，如：

// 调用公众号接口
$response = $officialAccount->getClient()->get('cgi-bin/users/list');
```

> `$officialAccount` 为 `EasyWeChat\OfficialAccount\Application` 实例

:book: 更多公众号用法请参考：[公众号](../official-account/index.md)

### 代小程序调用

小程序和公众号使用方式一样，同样有三种方式：

```php
// 方式一：使用 authorizer_refresh_token
$authorizerRefreshToken = '小程序授权时得到的 authorizer_refresh_token';
$officialAccount = $app->getMiniAppWithRefreshToken($appId, $authorizerRefreshToken);

// 方式二：使用 authorizer_access_token
$authorizerAccessToken = '小程序授权时得到的 authorizer_access_token';
$officialAccount = $app->getMiniAppWithAccessToken($appId, $authorizerAccessToken);

// 方式三：不推荐
// $token 为你存到数据库的授权码 authorizer_access_token
$authorizerAccessToken = new AuthorizerAccessToken($authorizerAppId, $token);
$miniApp = $app->getMiniApp($authorizerAccessToken);

// 调用小程序接口
$response = $miniApp->getClient()->get('cgi-bin/users/list');
```

- [微信官方文档 - 开放平台代小程序实现小程序登录接口](https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/others/WeChat_login.html#请求地址)

:book: 更多小程序用法请参考：[小程序](../mini-app/index.md)
