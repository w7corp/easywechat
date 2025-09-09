# 缓存

EasyWeChat 6.x 使用 [symfony/cache](https://github.com/symfony/cache) 组件来处理缓存，它支持目前几乎所有主流的缓存引擎。

在 SDK 中，所有缓存默认使用文件系统缓存，缓存路径取决于 PHP 的临时目录。如果你需要自定义缓存配置，可以通过简单的几个步骤来实现。

## 默认缓存行为

EasyWeChat 6.x 中的应用实例（如 `OfficialAccount\Application`、`MiniApp\Application` 等）都通过 `InteractWithCache` trait 提供了统一的缓存接口：

```php
use EasyWeChat\OfficialAccount\Application;

$app = new Application([
    'app_id' => 'your-app-id',
    'secret' => 'your-app-secret',
    // ...
]);

// 获取默认缓存实例
$cache = $app->getCache();

// 缓存默认配置
echo $app->getCacheLifetime(); // 1500 秒
echo $app->getCacheNamespace(); // 'easywechat'
```

## 缓存配置调整

你可以调整缓存的生命周期和命名空间：

```php
// 设置缓存生命周期为 3600 秒（1小时）
$app->setCacheLifetime(3600);

// 设置缓存命名空间
$app->setCacheNamespace('my_wechat_app');
```

## 使用 Redis 缓存

### 基础 Redis 配置

使用 Redis 作为缓存引擎是最常见的需求。首先安装 Redis 相关扩展：

```bash
composer require predis/predis
```

然后配置 Redis 缓存：

```php
use EasyWeChat\OfficialAccount\Application;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Psr16Cache;

// 创建应用实例
$app = new Application([
    'app_id' => 'your-app-id',
    'secret' => 'your-app-secret',
]);

// 创建 Redis 连接
$redis = new \Predis\Client([
    'scheme' => 'tcp',
    'host'   => '127.0.0.1',
    'port'   => 6379,
]);

// 创建 Redis 缓存适配器
$cache = new Psr16Cache(
    new RedisAdapter($redis, 'easywechat', 1500)
);

// 设置自定义缓存
$app->setCache($cache);
```

### 使用 Redis 集群

对于 Redis 集群环境：

```php
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Psr16Cache;

// Redis 集群配置
$redis = new \Predis\Client([
    [
        'scheme' => 'tcp',
        'host'   => '10.0.0.1',
        'port'   => 6379,
    ],
    [
        'scheme' => 'tcp', 
        'host'   => '10.0.0.2',
        'port'   => 6379,
    ],
], [
    'cluster' => 'redis',
]);

$cache = new Psr16Cache(
    new RedisAdapter($redis, 'easywechat_cluster', 1500)
);

$app->setCache($cache);
```

### 使用 phpredis 扩展

如果你使用 phpredis 扩展：

```php
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Psr16Cache;

// 使用 phpredis
$redis = new \Redis();
$redis->connect('127.0.0.1', 6379);
$redis->select(1); // 选择数据库

$cache = new Psr16Cache(
    new RedisAdapter($redis, 'easywechat', 1500)
);

$app->setCache($cache);
```

## 使用 Memcached 缓存

```php
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\Cache\Psr16Cache;

// 创建 Memcached 连接
$memcached = new \Memcached();
$memcached->addServer('127.0.0.1', 11211);

// 创建缓存实例
$cache = new Psr16Cache(
    new MemcachedAdapter($memcached, 'easywechat', 1500)
);

$app->setCache($cache);
```

## 在 Laravel 中使用

Laravel 框架提供了便捷的缓存管理，你可以直接使用 Laravel 的缓存驱动：

### 使用 Laravel Cache

```php
use Symfony\Component\Cache\Adapter\Psr6Adapter;
use Symfony\Component\Cache\Psr16Cache;

// 将 Laravel Cache 转换为 PSR-16 缓存
$cache = new Psr16Cache(
    new Psr6Adapter(
        app('cache')->store() // 使用默认缓存驱动
    )
);

$app->setCache($cache);
```

### 使用指定的 Laravel Cache Store

```php
// 使用 Redis 作为缓存驱动
$cache = new Psr16Cache(
    new Psr6Adapter(
        app('cache')->store('redis')
    )
);

$app->setCache($cache);
```

### Laravel 服务提供者中配置

在 Laravel 服务提供者中统一配置：

```php
// app/Providers/WeChatServiceProvider.php
use EasyWeChat\OfficialAccount\Application;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Cache\Adapter\Psr6Adapter;
use Symfony\Component\Cache\Psr16Cache;

class WeChatServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('wechat.official_account', function ($app) {
            $application = new Application(config('wechat.official_account'));
            
            // 使用 Laravel 缓存
            $cache = new Psr16Cache(
                new Psr6Adapter(
                    $app['cache']->store(config('wechat.cache_store', 'redis'))
                )
            );
            
            $application->setCache($cache);
            
            return $application;
        });
    }
}
```

## 自定义缓存驱动

如果现有的缓存驱动无法满足你的需求，你可以实现自己的缓存类，只需实现 [PSR-16](https://www.php-fig.org/psr/psr-16/) 规范即可。

### 实现 PSR-16 接口

PSR-16 `CacheInterface` 接口包含以下方法：

```php
interface CacheInterface
{
    public function get($key, $default = null);
    public function set($key, $value, $ttl = null);
    public function delete($key);
    public function clear();
    public function getMultiple($keys, $default = null);
    public function setMultiple($values, $ttl = null);
    public function deleteMultiple($keys);
    public function has($key);
}
```

### 自定义缓存实现示例

```php
use Psr\SimpleCache\CacheInterface;

class MyCustomCache implements CacheInterface
{
    private array $data = [];

    public function get($key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function set($key, $value, $ttl = null)
    {
        // 这里可以实现你的存储逻辑
        // 比如存储到数据库、文件或其他存储系统
        $this->data[$key] = $value;
        
        return true;
    }

    public function delete($key)
    {
        unset($this->data[$key]);
        return true;
    }

    public function clear()
    {
        $this->data = [];
        return true;
    }

    public function getMultiple($keys, $default = null)
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }
        return $result;
    }

    public function setMultiple($values, $ttl = null)
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }
        return true;
    }

    public function deleteMultiple($keys)
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }

    public function has($key)
    {
        $stmt = $this->pdo->prepare("
            SELECT 1 FROM cache_items 
            WHERE cache_key = ? AND (expires_at IS NULL OR expires_at > NOW())
        ");
        $stmt->execute([$key]);
        return $stmt->fetchColumn() !== false;
    }
}
```

### 使用自定义缓存

```php
// 实例化自定义缓存
$myCache = new MyCustomCache();

// 应用到 EasyWeChat
$app->setCache($myCache);
```

## 数据库缓存实现

下面是一个使用数据库作为缓存存储的完整示例：

```php
use Psr\SimpleCache\CacheInterface;

class DatabaseCache implements CacheInterface
{
    private \PDO $pdo;
    
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->createTable();
    }
    
    private function createTable()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS cache_items (
                cache_key VARCHAR(255) PRIMARY KEY,
                cache_value TEXT,
                expires_at TIMESTAMP NULL
            )
        ";
        $this->pdo->exec($sql);
    }

    public function get($key, $default = null)
    {
        $stmt = $this->pdo->prepare("
            SELECT cache_value FROM cache_items 
            WHERE cache_key = ? AND (expires_at IS NULL OR expires_at > NOW())
        ");
        $stmt->execute([$key]);
        
        $result = $stmt->fetchColumn();
        
        if ($result === false) {
            return $default;
        }
        
        return unserialize($result);
    }

    public function set($key, $value, $ttl = null)
    {
        $expiresAt = $ttl ? date('Y-m-d H:i:s', time() + $ttl) : null;
        
        $stmt = $this->pdo->prepare("
            REPLACE INTO cache_items (cache_key, cache_value, expires_at) 
            VALUES (?, ?, ?)
        ");
        
        return $stmt->execute([
            $key, 
            serialize($value), 
            $expiresAt
        ]);
    }

    public function delete($key)
    {
        $stmt = $this->pdo->prepare("DELETE FROM cache_items WHERE cache_key = ?");
        return $stmt->execute([$key]);
    }

    public function clear()
    {
        return $this->pdo->exec("DELETE FROM cache_items") !== false;
    }

    public function getMultiple($keys, $default = null)
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }
        return $result;
    }

    public function setMultiple($values, $ttl = null)
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }
        return true;
    }

    public function deleteMultiple($keys)
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }

    public function has($key)
    {
        $stmt = $this->pdo->prepare("
            SELECT 1 FROM cache_items 
            WHERE cache_key = ? AND (expires_at IS NULL OR expires_at > NOW())
        ");
        $stmt->execute([$key]);
        return $stmt->fetchColumn() !== false;
    }
}

// 使用示例
$pdo = new PDO('mysql:host=localhost;dbname=test', 'user', 'pass');
$cache = new DatabaseCache($pdo);

$app->setCache($cache);
```

## 缓存性能优化建议

### 1. 选择合适的缓存驱动

- **Redis**: 适用于分布式环境，支持丰富的数据结构
- **Memcached**: 简单高效，适用于纯缓存场景
- **文件缓存**: 适用于单机环境或小型应用
- **数据库缓存**: 适用于需要持久化的场景

### 2. 合理设置缓存时间

```php
// 根据数据更新频率设置不同的缓存时间
$app->setCacheLifetime(7200); // Access Token 缓存 2 小时
```

### 3. 使用缓存前缀避免冲突

```php
// 为不同环境设置不同的命名空间
$app->setCacheNamespace('easywechat_prod'); // 生产环境
$app->setCacheNamespace('easywechat_dev');  // 开发环境
```

### 4. 监控缓存命中率

```php
// 在自定义缓存中添加统计功能
class MonitoredCache implements CacheInterface 
{
    private $hits = 0;
    private $misses = 0;
    
    public function get($key, $default = null) 
    {
        $value = $this->actualCache->get($key, $default);
        
        if ($value === $default) {
            $this->misses++;
        } else {
            $this->hits++;
        }
        
        return $value;
    }
    
    public function getHitRate(): float
    {
        $total = $this->hits + $this->misses;
        return $total > 0 ? $this->hits / $total : 0;
    }
}
```

## 常见问题

### Q: 如何清空 EasyWeChat 的缓存？

```php
// 清空当前应用的所有缓存
$app->getCache()->clear();

// 删除特定缓存项
$app->getCache()->delete('specific_key');
```

### Q: 如何在不同的应用间共享缓存？

```php
// 使用相同的缓存实例
$sharedCache = new Psr16Cache(new RedisAdapter($redis));

$officialAccount->setCache($sharedCache);
$miniApp->setCache($sharedCache);
```

### Q: 缓存键冲突怎么办？

```php
// 为不同应用设置不同的命名空间
$officialAccount->setCacheNamespace('wechat_oa');
$miniApp->setCacheNamespace('wechat_mini');
```

通过合理配置缓存，你可以显著提升 EasyWeChat 应用的性能和响应速度。选择适合你业务场景的缓存方案，并根据实际使用情况进行优化调整。