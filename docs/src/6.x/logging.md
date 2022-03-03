# 日志

如果没有在配置中指定日志选项，将不会记录任何日志。仅在配置了相关日志策略时启用。

## 日志配置

你可以配置多个日志的 `channel`，每个 `channel` 里的 `driver` 对应不同的日志驱动，内置可用的 `driver` 如下表：

| 名称       | 描述                                                                                                                                                        |
| ---------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `stack`    | 复合型，可以包含下面多种驱动的混合模式                                                                                                                      |
| `single`   | 基于 `StreamHandler` 的单一文件日志，参数有 `path`，`level`                                                                                                 |
| `daily`    | 基于 `RotatingFileHandler` 按日期生成日志文件，参数有 `path`，`level`，`days`(默认 7 天)                                                                    |
| `slack`    | 基于 `SlackWebhookHandler` 的 Slack 组件，参数请参考源码：[LogManager.php](https://github.com/w7corp/wechat/blob/master/src/Kernel/Log/LogManager.php#L247) |
| `syslog`   | 基于 `SyslogHandler` Monolog 驱动，参数有 `facility` 默认为 `LOG_USER`，`level`                                                                             |
| `errorlog` | 记录日志到系统错误日志，基于 `ErrorLogHandler`，参数有 `type`，默认为 `ErrorLogHandler::OPERATING_SYSTEM`                                                   |

### 自定义日志驱动

由于日志使用的是 [Monolog](https://github.com/Seldaek/monolog)，所以，除了默认的文件式日志外，你可以自定义日志处理器：

```php
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;


// 注册自定义日志
$app->getLogger()->extend('mylog', function($app, $config){
    return new Logger($this->parseChannel($config), [
        $this->prepareHandler(new RotatingFileHandler(
            $config['path'], $config['days'], $this->level($config)
        )),
    ]);
});
```

> 在你自定义的闭包函数中，可以使用 `EasyWeChat\Kernel\Log\LogManager` 中的方法，具体请查看 SDK 源代码。

配置文件中在 `driver` 部分即可使用你自定义的驱动了：

```php
'logging' => [
    'default' => 'dev', // 默认使用的 channel，生产环境可以改为下面的 prod
    'channels' => [
        // 测试环境
        'dev' => [
            'driver' => 'mylog',
            'path' => '/tmp/easywechat.log',
            'level' => 'debug',
            'days' => 5,
        ],

        //...
    ],
],
```
