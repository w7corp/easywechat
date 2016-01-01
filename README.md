# [WeChat SDK](http://easywechat.org)

> 当前版本为 3.0 开发中,如果您愿意加入到开发版本测试当中，那必然是极好的事儿，测试方法：

> 1. 把你项目的 composer.json 中的 "minimum-stability" 改成 "dev":
   ```
   "minimum-stability":"dev",
   ```
> 2. `composer require "overtrue/wechat:develop-dev" -vvv` 安装 3.0 分支
> 3. 试用并[反馈给我](https://github.com/overtrue/wechat/issues) ，请以"[3.0]"开头，谢谢！

可能是目前最优雅的微信公众平台 SDK 了。[Laravel 5 拓展包: overtrue/laravel-wechat](https://github.com/overtrue/laravel-wechat)

SDK 使用交流 QQ 群：`319502940`

微信开发者交流 QQ 群：`9179779`

[![Build Status](https://travis-ci.org/overtrue/wechat.svg?branch=master)](https://travis-ci.org/overtrue/wechat)
[![Latest Stable Version](https://poser.pugx.org/overtrue/wechat/v/stable.svg)](https://packagist.org/packages/overtrue/wechat)
[![Latest Unstable Version](https://poser.pugx.org/overtrue/wechat/v/unstable.svg)](https://packagist.org/packages/overtrue/wechat)
[![Build Status](https://scrutinizer-ci.com/g/overtrue/wechat/badges/build.png?b=master)](https://scrutinizer-ci.com/g/overtrue/wechat/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/overtrue/wechat/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/overtrue/wechat/?branch=master)
[![Total Downloads](https://poser.pugx.org/overtrue/wechat/downloads)](https://packagist.org/packages/overtrue/wechat)
[![License](https://poser.pugx.org/overtrue/wechat/license)](https://packagist.org/packages/overtrue/wechat)

网上充斥着各种微信 SDK，但是找了一圈，发现没有一个想用，因为没有满足本项目存在后的各种优点：

 - 命名不那么乱七八糟；
 - 隐藏开发者不需要关注的细节；
 - 方法使用更优雅，不必再去研究那些奇怪的的方法名或者类名是做啥用的；
 - 自定义缓存方式；
 - 符合 [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md) 标准，你可以各种方便的与你的框架集成；
 - 高度抽象的消息类，免去各种拼json与xml的痛苦；
 - 详细 Debug 日志，一切交互都一目了然；

## 安装

环境要求：PHP >= 5.5.9

1. 使用 [composer](https://getcomposer.org/)

  ```shell
  composer require "overtrue/wechat:~3.0"
  ```

## 使用

基本使用（以服务端为例）:

```php
<?php

use EasyWeChat\Foundation\Application;

$options = [
    'debug'     => true,
    'app_id'    => 'wx3cf0f39249eb0e60',
    'secret'    => 'f1c242f4f28f735d4687abb469072a29',
    'token'     => 'easywechat',
    'log' => [
        'level' => \Monolog\Logger::DEBUG,
        'file'  => '/tmp/easywechat.log',
    ],
    // ...
];

$app = new Application($options);

$server = $app['server'];
$user = $app['user'];

$server->setMessageHandler(function($message) use ($user) {
    $fromUser = $user->get($message->FromUserName);

    return "{$fromUser->nickname} 您好！欢迎关注 overtrue!";
});

$server->serve()->send();
```

更多请参考文档。

## 文档

[http://easywechat.org/](http://easywechat.org/)

> 强烈建议看懂微信文档后再来使用本 SDK。

## 贡献代码

非常欢迎大家贡献代码共同完善本项目，烦请遵循 [PSR标准](https://github.com/php-fig/fig-standards/blob/master/accepted/) 谢谢！

## License

MIT
