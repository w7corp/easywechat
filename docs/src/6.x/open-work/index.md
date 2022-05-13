# 企业微信服务商

请仔细阅读并理解：[企业微信 API - 第三方应用开发](https://open.work.weixin.qq.com/api/doc/90001/90142/90594)

## 实例化

```php
<?php
use EasyWeChat\OpenWork\Application;

$config = [
  'corp_id' => 'wx3cf0f39249eb0exx',
  'provider_secret' => 'f1c242f4f28f735d4687abb469072axx',
  'token' => 'easywechat',
  'aes_key' => '', // 明文模式请勿填写 EncodingAESKey

  /**
   * 接口请求相关配置，超时时间等，具体可用参数请参考：
   * https://github.com/symfony/symfony/blob/5.3/src/Symfony/Contracts/HttpClient/HttpClientInterface.php
   */
  'http' => [
      'throw'  => true, // 状态码非 200、300 时是否抛出异常，默认为开启
      'timeout' => 5.0,
      // 'base_uri' => 'https://qyapi.weixin.qq.com/', // 如果你在国外想要覆盖默认的 url 的时候才使用，根据不同的模块配置不同的 uri

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

### ProviderAccessToken

provider_access_token 是开放平台 API 调用的必备条件，如果你想获取它的值，你可以通过以下方式拿到当前的 provider_access_token：

```php
$providerAccessToken = $app->getProviderAccessToken();
$providerAccessToken->getToken(); // string
```

当然你也可以使用自己的 ProviderAccessToken 类：

```php
$providerAccessToken = new MyCustomProviderAccessToken();
$app->setProviderAccessToken($providerAccessToken)
```

### SuiteTicket

你可以通过以下方式拿到当前 suite_ticket 类：

```php
$suiteTicket = $app->getSuiteTicket();

$suiteTicket->getTicket(); // string
```

### 开放平台账户

开放平台账号类，提供一系列 API 获取开放平台的基本信息：

```php
$account = $app->getAccount();

$account->getCorpId();
$account->getProviderSecret();
$account->getToken();
$account->getAesKey();
```

## 第三方应用需要在打开的网页里面携带用户的身份信息

> [点此查看官方文档](https://open.work.weixin.qq.com/api/doc/90001/90143/91120#%E6%9E%84%E9%80%A0%E7%AC%AC%E4%B8%89%E6%96%B9%E5%BA%94%E7%94%A8oauth2%E9%93%BE%E6%8E%A5)

第三方应用或者网站网页授权的逻辑和公众号的网页授权基本一样：

```php
$oauth = $app->getOAuth(string $suiteId, AccessTokenInterface $suiteAccessToken);
```

:book: 详情请参考：[网页授权](./oauth.md)

## 企业网页授权

> [点此查看官方文档](https://open.work.weixin.qq.com/api/doc/90001/90143/91120#%E6%9E%84%E9%80%A0%E4%BC%81%E4%B8%9Aoauth2%E9%93%BE%E6%8E%A5)

```php
$oauth = $app->getCorpOAuth(string $corpId, AccessTokenInterface $suiteAccessToken);
```

:book: 详情请参考：[网页授权](./oauth.md)

## 使用授权码获取授权信息

在用户在授权页授权流程完成后，授权页会自动跳转进入回调 URI，并在 URL 参数中返回授权码和过期时间，如：(`https://easywechat.com/callback?auth_code=xxx&expires_in=600`)

```php
$permanentCode = '企业永久授权码';
$suiteAccessToken = new SuiteAccessToken($suiteId, $suiteSecret);

$authorization = $app->getAuthorization($corpId, $authorizatpermanentCodeionCode, $suiteAccessToken);

$authorization->getCorpId(); // auth_corp_info.corpid
$authorization->toArray();
$authorization->toJson();

// {
//     "errcode":0 ,
//     "errmsg":"ok" ,
//     "dealer_corp_info":
//     {
//         "corpid": "xxxx",
//         "corp_name": "name"
//     },
//     "auth_corp_info":
//     {
//         "corpid": "xxxx",
//         "corp_name": "name",
//         "corp_type": "verified",
//         "corp_square_logo_url": "yyyyy",
//         "corp_user_max": 50,
//         "corp_agent_max": 30,
//         "corp_full_name":"full_name",
//         "verified_end_time":1431775834,
//         "subject_type": 1,
//         "corp_wxqrcode": "zzzzz",
//         "corp_scale": "1-50人",
//         "corp_industry": "IT服务",
//         "corp_sub_industry": "计算机软件/硬件/信息服务",
//         "location":"广东省广州市"
//     },
//     "auth_info":
//     {
//         "agent" :
//         [
//             {
//                 "agentid":1,
//                 "name":"NAME",
//                 "round_logo_url":"xxxxxx",
//                 "square_logo_url":"yyyyyy",
//                 "appid":1,
//                 "auth_mode":1,
//                 "privilege":
//                 {
//                     "level":1,
//                     "allow_party":[1,2,3],
//                     "allow_user":["zhansan","lisi"],
//                     "allow_tag":[1,2,3],
//                     "extra_party":[4,5,6],
//                     "extra_user":["wangwu"],
//                     "extra_tag":[4,5,6]
//                 },
//                 "shared_from":
//                 {
//                     "corpid":"wwyyyyy"
//                 }
//             },
//             {
//                 "agentid":2,
//                 "name":"NAME2",
//                 "round_logo_url":"xxxxxx",
//                 "square_logo_url":"yyyyyy",
//                 "appid":5,
//                 "shared_from":
//                 {
//                     "corpid":"wwyyyyy"
//                 }
//             }
//         ]
//     }
// }

```

## 获取企业凭证

在公众号/小程序接口调用令牌（`authorizer_access_token`）失效时，可以使用刷新令牌（authorizer_refresh_token）获取新的接口调用令牌。

> 注意： `authorizer_access_token` 有效期为 2 小时，开发者需要缓存 `authorizer_access_token`，避免获取/刷新接口调用令牌的 API 调用触发每日限额。缓存方法可以参考：<https://developers.weixin.qq.com/doc/offiaccount/Basic_Information/Get_access_token.html>

```php
$permanentCode = '企业永久授权码';
$suiteAccessToken = new SuiteAccessToken($suiteId, $suiteSecret);

$authorizerAccessToken = $app->getAuthorizerAccessToken($corpId, $permanentCode, $suiteAccessToken)

// {
//     "errcode":0 ,
//     "errmsg":"ok" ,
//     "access_token": "xxxxxx",
//     "expires_in": 7200
// }


$authorizerAccessToken->getToken(); // string
```
