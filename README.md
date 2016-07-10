# [Easy WeChat](http://easywechat.org)

可能是目前最优雅的微信公众平台 SDK 了。

> 不支持企业号，也不打算支持，原因？微信的API实在设计得太乱了，我怕累死。。。

- SDK 官方论坛：https://forum.easywechat.org
- SDK 使用交流 QQ 群：`319502940`
- 微信开发者交流 QQ 群：`9179779`

[![Build Status](https://travis-ci.org/overtrue/wechat.svg?branch=master)](https://travis-ci.org/overtrue/wechat)
[![Latest Stable Version](https://poser.pugx.org/overtrue/wechat/v/stable.svg)](https://packagist.org/packages/overtrue/wechat)
[![Latest Unstable Version](https://poser.pugx.org/overtrue/wechat/v/unstable.svg)](https://packagist.org/packages/overtrue/wechat)
[![Build Status](https://scrutinizer-ci.com/g/overtrue/wechat/badges/build.png?b=master)](https://scrutinizer-ci.com/g/overtrue/wechat/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/overtrue/wechat/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/overtrue/wechat/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/overtrue/wechat/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/overtrue/wechat/?branch=master)
[![Total Downloads](https://poser.pugx.org/overtrue/wechat/downloads)](https://packagist.org/packages/overtrue/wechat)
[![License](https://poser.pugx.org/overtrue/wechat/license)](https://packagist.org/packages/overtrue/wechat)

## 特点

 - 命名不那么乱七八糟；
 - 隐藏开发者不需要关注的细节；
 - 方法使用更优雅，不必再去研究那些奇怪的的方法名或者类名是做啥用的；
 - 自定义缓存方式；
 - 符合 [PSR](https://github.com/php-fig/fig-standards) 标准，你可以各种方便的与你的框架集成；
 - 高度抽象的消息类，免去各种拼json与xml的痛苦；
 - 详细 Debug 日志，一切交互都一目了然；

## 环境要求

1. PHP >= 5.5.9
2. **[composer](https://getcomposer.org/)**
3. openssl 拓展
4. fileinfo 拓展（素材管理模块需要用到）

> SDK 对所使用的框架并无特别要求

## 安装

```shell
composer require "overtrue/wechat:~3.1" -vvv
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
        'level' => 'debug',
        'file'  => '/tmp/easywechat.log',
    ],
    // ...
];

$app = new Application($options);

$server = $app->server;
$user = $app->user;

$server->setMessageHandler(function($message) use ($user) {
    $fromUser = $user->get($message->FromUserName);

    return "{$fromUser->nickname} 您好！欢迎关注 overtrue!";
});

$server->serve()->send();
```

更多请参考[http://easywechat.org/](http://easywechat.org/)。

## 文档

[http://easywechat.org/](http://easywechat.org/)

> 强烈建议看懂微信文档后再来使用本 SDK。

## 框架集成

[Laravel 5 拓展包: overtrue/laravel-wechat](https://github.com/overtrue/laravel-wechat)

## 贡献代码

[贡献指南](CONTRIBUTING.md)

## License

MIT
