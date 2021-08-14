# 缓存


本项目使用 [doctrine/cache](https://github.com/doctrine/cache) 来完成缓存工作，它支持基本目前所有的缓存引擎。

在我们的 SDK 中的所有缓存默认使用文件缓存，缓存路径取决于 PHP 的临时目录，如果你需要自定义缓存，那么你需要做如下的事情：

你可以参考[doctrine/cache官方文档](http://doctrine-orm.readthedocs.org/projects/doctrine-orm/en/latest/reference/caching.html)来替换掉应用中默认的缓存配置：

> 以 redis 为例
> 请先安装 redis 拓展：https://github.com/phpredis/phpredis

```php

use Doctrine\Common\Cache\RedisCache;

$cacheDriver = new RedisCache();

// 创建 redis 实例
$redis = new Redis();
$redis->connect('redis_host', 6379);

$cacheDriver->setRedis($redis);

$options = [
    'debug'  => false,
    'app_id' => $wechatInfo['app_id'],
    'secret' => $wechatInfo['app_secret'],
    'token'  => $wechatInfo['token'],
    'aes_key' => $wechatInfo['aes_key'], // 可选
    'cache'   => $cacheDriver,
];

$wechatApp = new Application($options);
```

### Laravel 中使用

在 Laravel 中框架使用 [predis/predis](https://github.com/nrk/predis)，那么我们就得使用 `Doctrine\Common\Cache\PredisCache`：

```php

use Doctrine\Common\Cache\PredisCache;

$predis = app('redis')->connection();// connection($name), $name 默认为 `default`
$cacheDriver = new PredisCache($predis);

$app->cache = $cacheDriver;
```

> 上面提到的 `app('redis')->connection($name)`, 这里的 `$name` 是 laravel 项目中配置文件 `database.php` 中 `redis` 配置名 `default`：https://github.com/laravel/laravel/blob/master/config/database.php#L118
> 如果你使用的其它连接，对应传名称就好了。
> 如果你在使用Laravel 5.4，应将`$predis = app('redis')->connection();`修改为：`$predis = app('redis')->connection()->client();`

## 使用自定义的缓存方式

如果你发现 doctrine 提供的几十种缓存方式都满足不了你的需求的话，那么你可以自己建立一个类来完成缓存操作，前提这个类得实现接口：[Doctrine\Common\Cache\Cache](https://github.com/doctrine/cache/blob/master/lib/Doctrine/Common/Cache/Cache.php)

该接口有以下方法需要实现：

```php
   public function fetch($id);    // 读取缓存
   public function contains($id);  // 检查是否存在缓存
   public function save($id, $data, $lifeTime = 0);   // 设置缓存
   public function delete($id);  // 删除缓存
   public function getStats(); // 获取状态
```

下面为一个示例：

```php
<?php

use Doctrine\Common\Cache\Cache as CacheInterface;

class MyCacheDriver implements CacheInterface
{
    public function fetch($id)
    {
        // 你自己从你想实现的存储方式读取并返回
    }

    public function contains($id)
    {
        // 同理 返回存在与否 bool 值
    }

    public function save($id, $data, $lifeTime = 0)
    {
        // 用你的方式存储该缓存内容即可
    }

    public function delete($id)
    {
        // 删除并返回 bool 值
    }

    public function getStats()
    {
        // 这个你可以不用实现，返回 null 即可
    }
}
```

然后实例化你的缓存类并在 EasyWeChat 里使用它：

```php
$myCacheDriver = new MyCacheDriver();

$config = [
    //...
    'cache'   => $myCacheDriver,
];

$wechatApp = new Application($options);
```

OK，这样就完成了自定义缓存的操作。
