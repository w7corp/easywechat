<p align="center">
<a href="https://easywechat.org/">
<img src="https://easywechat.org/logo.svg" alt="EasyWeChat" width="160">
</a>

<p align="center">📦 It is probably the best SDK in the world for developing Wechat App.</p>

<p align="center">
<a href="https://travis-ci.org/overtrue/wechat"><img src="https://travis-ci.org/overtrue/wechat.svg?branch=master" alt="Build Status"></a>
<a href="https://packagist.org/packages/overtrue/wechat"><img src="https://poser.pugx.org/overtrue/wechat/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/overtrue/wechat"><img src="https://poser.pugx.org/overtrue/wechat/v/unstable.svg" alt="Latest Unstable Version"></a>
<a href="https://scrutinizer-ci.com/g/overtrue/wechat/build-status/master"><img src="https://scrutinizer-ci.com/g/overtrue/wechat/badges/build.png?b=master" alt="Build Status"></a>
<a href="https://scrutinizer-ci.com/g/overtrue/wechat/?branch=master"><img src="https://scrutinizer-ci.com/g/overtrue/wechat/badges/quality-score.png?b=master" alt="Scrutinizer Code Quality"></a>
<a href="https://scrutinizer-ci.com/g/overtrue/wechat/?branch=master"><img src="https://scrutinizer-ci.com/g/overtrue/wechat/badges/coverage.png?b=master" alt="Code Coverage"></a>
<a href="https://packagist.org/packages/overtrue/wechat"><img src="https://poser.pugx.org/overtrue/wechat/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/overtrue/wechat"><img src="https://poser.pugx.org/overtrue/wechat/license" alt="License"></a>
</p>

</div>

<p align="center">
    <b>Special thanks to the generous sponsorship by:</b>
    <br><br>
    <a href="https://www.yousails.com">
      <img src="https://yousails.com/banners/brand.png" width=350>
    </a>
    <br><br>
    <a href="https://laravist.com">
      <img width="160" src="https://o0dpls1ru.qnssl.com/laravist.com-logo.png">
    </a>
</p>



## Feature

 - 命名不那么乱七八糟；
 - 隐藏开发者不需要关注的细节；
 - 方法使用更优雅，不必再去研究那些奇怪的的方法名或者类名是做啥用的；
 - 自定义缓存方式；
 - 符合 [PSR](https://github.com/php-fig/fig-standards) 标准，你可以各种方便的与你的框架集成；
 - 高度抽象的消息类，免去各种拼json与xml的痛苦；
 - 详细 Debug 日志，一切交互都一目了然；

## Requirement

1. PHP >= 5.5.9
2. **[composer](https://getcomposer.org/)**
3. openssl 拓展
4. fileinfo 拓展（素材管理模块需要用到）

> SDK 对所使用的框架并无特别要求

## Installation

```shell
composer require "overtrue/wechat:~3.1" -vvv
```

## Usage

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

## Documentation

- Homepage: http://easywechat.org
- Forum: https://forum.easywechat.org
- 微信公众平台文档: https://mp.weixin.qq.com/wiki
- WeChat Official Documentation: http://admin.wechat.com/wiki

> 强烈建议看懂微信文档后再来使用本 SDK。

## Integration

[Laravel 5 拓展包: overtrue/laravel-wechat](https://github.com/overtrue/laravel-wechat)

## Contribution

[Contribution Guide](.github/CONTRIBUTING.md)

## License

MIT
