# OAuth

第三方服务商网页授权有两种：

- [第三方应用网页授权](https://open.work.weixin.qq.com/api/doc/90001/90143/91120#%E6%9E%84%E9%80%A0%E7%AC%AC%E4%B8%89%E6%96%B9%E5%BA%94%E7%94%A8oauth2%E9%93%BE%E6%8E%A5)
- [企业网页授权](https://open.work.weixin.qq.com/api/doc/90001/90143/91120#%E6%9E%84%E9%80%A0%E4%BC%81%E4%B8%9Aoauth2%E9%93%BE%E6%8E%A5)

创建实例：

```php
use EasyWeChat\work\Application;

$config = [
    'corp_id' => 'xxxxxxxxxxxxxxxxx',
    'secret'   => 'xxxxxxxxxx', // 应用的 secret
];

$app = new Application($config);
```

## 获取 OAuth 模块实例

请根据你的场景选择对应的方法获取 OAuth 实例：

```php
// 第三方应用网页授权
$oauth = $app->getOAuth(string $suiteId, AccessTokenInterface $suiteAccessToken);

// 企业网页授权
$oauth = $app->getCorpOAuth(string $corpId, AccessTokenInterface $suiteAccessToken);
// 如需指定应用ID
$oauth = $oauth->withAgentId($agentId);
```

## 跳转授权

```php
// $callbackUrl 为授权回调地址
$callbackUrl = 'https://xxx.xxx'; // 需设置可信域名

// 返回授权跳转链接
$redirectUrl = $app->getOAuth()->redirect($callbackUrl);
```

## 获取授权用户信息

在回调页面中，你可以使用以下方式获取授权者信息：

```php
$code = "回调URL中的code";
$user = $app->getOAuth()->detailed()->userFromCode($code);

// 获取用户信息
$user->getId(); // 对应企业微信英文名（userid）
$user->getRaw(); // 获取企业微信接口返回的原始信息
```

:book: OAuth 详情请参考：[网页授权](../oauth.md)

获取用户其他信息需调用通讯录接口，参考：[企业微信通讯录 API](https://github.com/EasyWeChat/docs/blob/master/wework/contacts.md)

## 参考阅读

- 本模块基于 [overtrue/socialite](https://github.com/overtrue/socialite/) 实现，更多的使用请阅读该扩展包文档。
- state 参数的使用: [overtrue/socialite/#state](https://github.com/overtrue/socialite/#state)
