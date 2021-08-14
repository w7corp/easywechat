# 缓存


本项目使用 [symfony/cache](https://github.com/symfony/cache) 来完成缓存工作，它支持基本目前所有的缓存引擎。

在我们的 SDK 中的所有缓存默认使用文件缓存，缓存路径取决于 PHP 的临时目录，如果你需要自定义缓存，那么你需要做如下的事情：

你可以参考[symfony/cache官方文档](https://symfony.com/doc/current/components/cache.html) 来替换掉应用中默认的缓存配置：


## 以 redis 为例


### Symfony 4.3 + 

> 请先安装 redis 拓展：`composer require predis/predis`

```php

use Symfony\Component\Cache\Adapter\RedisAdapter;

// 创建 redis 实例
$client = new \Predis\Client('tcp://10.0.0.1:6379');

// 创建缓存实例
$cache = new RedisAdapter($client);

// 替换应用中的缓存
$app->rebind('cache', $cache);
```

### Symfony 3.4 + 

> 请先安装 redis 拓展：https://github.com/phpredis/phpredis

```php

use Symfony\Component\Cache\Simple\RedisCache;

// 创建 redis 实例
$redis = new Redis();
$redis->connect('redis_host', 6379);

// 创建缓存实例
$cache = new RedisCache($redis);

// 替换应用中的缓存
$app->rebind('cache', $cache);
```


### Laravel 中使用

在 Laravel 中框架使用 [predis/predis](https://github.com/nrk/predis)：

### Symfony 4.3 + 

> 请先安装 redis 拓展：`composer require predis/predis`

```php

use Symfony\Component\Cache\Adapter\RedisAdapter;

// 创建缓存实例
$cache = new RedisAdapter(app('redis')->connection()->client());
$app->rebind('cache', $cache);

```

### Symfony 3.4 + 

```php

use Symfony\Component\Cache\Simple\RedisCache;

$predis = app('redis')->connection()->client(); // connection($name), $name 默认为 `default`
$cache = new RedisCache($predis);

$app->rebind('cache', $cache);
```

> 上面提到的 `app('redis')->connection($name)`, 这里的 `$name` 是 laravel 项目中配置文件 `database.php` 中 `redis` 配置名 `default`：https://github.com/laravel/laravel/blob/master/config/database.php#L118
> 如果你使用的其它连接，对应传名称就好了。

## 使用自定义的缓存方式

如果你发现 symfony 提供的十几种缓存方式都满足不了你的需求的话，那么你可以自己建立一个类来完成缓存操作，前提这个类得实现接口：[PSR-16](http://www.php-fig.org/psr/psr-16/)

该接口有以下方法需要实现：

```php
   public function get($key, $default = null);
   public function set($key, $value, $ttl = null);
   public function delete($key);
   public function clear();
   public function getMultiple($keys, $default = null);
   public function setMultiple($values, $ttl = null);
   public function deleteMultiple($keys);
   public function has($key);
```

下面为一个示例：

```php
<?php

use Psr\SimpleCache\CacheInterface;

class MyCustomCache implements CacheInterface
{
    public function get($key, $default = null)
    {
        // your code
    }

    public function set($key, $value, $ttl = null)
    {
        // your code
    }

    public function delete($key)
    {
        // your code
    }

    public function clear()
    {
        // your code
    }

    public function getMultiple($keys, $default = null)
    {
        // your code
    }

    public function setMultiple($values, $ttl = null)
    {
        // your code
    }

    public function deleteMultiple($keys)
    {
        // your code
    }

    public function has($key)
    {
        // your code
    }
}
```

然后实例化你的缓存类并在 EasyWeChat 里使用它：

```php
$app->rebind('cache', new MyCustomCache());
```

OK，这样就完成了自定义缓存的操作。
