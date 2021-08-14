# Access Token


SDK 中有一个 [Access Token](https://github.com/overtrue/wechat/blob/master/src/Core/AccessToken.php) 对象，它是一个全局使用的东西，请把它与 OAuth 中的 code 换取的 Access Token 区别开。

我们一个 SDK 应用在初始化以后，你可以在任何时机从应用中拿到该配置下的 Access Token 实例：

```php
use EasyWeChat\Foundation\Application;

$options = [
    //...
];

$app = new Application($options);

// 获取 access token 实例
$accessToken = $app->access_token; // EasyWeChat\Core\AccessToken 实例
$token = $accessToken->getToken(); // token 字符串
$token = $accessToken->getToken(true); // 强制重新从微信服务器获取 token.
```

## 修改 `$app` 的 Access Token

```php
$app['access_token']->setToken($newAccessToken, $expires);
```

例如：

```php
$app['access_token']->setToken('ccfdec35bd7ba359f6101c2da321d675');
// 或者指定过期时间
$app['access_token']->setToken('ccfdec35bd7ba359f6101c2da321d675', 3600);  // 单位：秒
```

## 设置 AccessToken 的缓存

你也可以自定义 token 的缓存方式，把一个实现了 `Doctrine\Common\Cache\Cache` 缓存接口的实例作为 AccessToken 构造函数的第三个参数传入即可：

本项目使用 [doctrine/cache](https://github.com/doctrine/cache) 来完成缓存工作，它支持几乎目前所有的缓存引擎。

以 Redis 为例：

```php

use Doctrine\Common\Cache\RedisCache; // RedisCache 实例了 `Doctrine\Common\Cache\Cache` 接口

$cache = new RedisCache();

// 创建 redis 实例
$redis = new Redis();
$redis->connect('redis_host', 6379);

$cache->setRedis($redis);

$app->access_token->setCache($cache);
```

