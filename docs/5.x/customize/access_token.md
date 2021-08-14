# Access Token


我们一个 SDK 应用在初始化以后，你可以在任何时机从应用中拿到该配置下的 Access Token 实例：

```php
use EasyWeChat\Factory;

$config = [
    //...
];

$app = Factory::officialAccount($config);

// 获取 access token 实例
$accessToken = $app->access_token;
$token = $accessToken->getToken(); // token 数组  token['access_token'] 字符串
$token = $accessToken->getToken(true); // 强制重新从微信服务器获取 token.
```

## 修改 `$app` 的 Access Token

```php
$app['access_token']->setToken($newAccessToken, 7200);
```

例如：

```php
$app['access_token']->setToken('ccfdec35bd7ba359f6101c2da321d675');
// 或者指定过期时间
$app['access_token']->setToken('ccfdec35bd7ba359f6101c2da321d675', 3600);  // 单位：秒
```
