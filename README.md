<img align="right" width="100" src="https://user-images.githubusercontent.com/1472352/49656357-1e874080-fa78-11e8-80ea-69e2103345cf.png" alt="EasyWeChat Logo"/>

<h1 align="left"><a href="https://www.easywechat.com">EasyWeChat</a></h1>

📦 It is probably the best SDK in the world for developing Wechat App.

[![Test Status](https://github.com/overtrue/wechat/workflows/Test/badge.svg)](https://github.com/overtrue/wechat/actions) 
[![Lint Status](https://github.com/overtrue/wechat/workflows/Lint/badge.svg)](https://github.com/overtrue/wechat/actions) 
[![Latest Stable Version](https://poser.pugx.org/overtrue/wechat/v/stable.svg)](https://packagist.org/packages/overtrue/wechat) 
[![Latest Unstable Version](https://poser.pugx.org/overtrue/wechat/v/unstable.svg)](https://packagist.org/packages/overtrue/wechat)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/overtrue/wechat/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/overtrue/wechat/?branch=master) 
[![Code Coverage](https://scrutinizer-ci.com/g/overtrue/wechat/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/overtrue/wechat/?branch=master) 
[![Total Downloads](https://poser.pugx.org/overtrue/wechat/downloads)](https://packagist.org/packages/overtrue/wechat) 
[![License](https://poser.pugx.org/overtrue/wechat/license)](https://packagist.org/packages/overtrue/wechat) 


## Requirement

1. PHP >= 7.1
2. **[Composer](https://getcomposer.org/)**
3. openssl 拓展
4. fileinfo 拓展（素材管理模块需要用到）

## Installation

```shell
$ composer require "overtrue/wechat:^4.2" -vvv
```

## Usage

基本使用（以服务端为例）:

```php
<?php

use EasyWeChat\Factory;

$options = [
    'app_id'    => 'wx3cf0f39249eb0exxx',
    'secret'    => 'f1c242f4f28f735d4687abb469072xxx',
    'token'     => 'easywechat',
    'log' => [
        'level' => 'debug',
        'file'  => '/tmp/easywechat.log',
    ],
    // ...
];

$app = Factory::officialAccount($options);

$server = $app->server;
$user = $app->user;

$server->push(function($message) use ($user) {
    $fromUser = $user->get($message['FromUserName']);

    return "{$fromUser->nickname} 您好！欢迎关注 overtrue!";
});

$server->serve()->send();
```

更多请参考 [https://www.easywechat.com/](https://www.easywechat.com/)。

## Documentation

[官网](https://www.easywechat.com)  · [教程](https://www.easywechat.com/tutorials)  ·  [讨论](https://yike.io/)  ·  [微信公众平台](https://mp.weixin.qq.com/wiki)  ·  [WeChat Official](http://admin.wechat.com/wiki)

## Integration

[Laravel 5 拓展包: overtrue/laravel-wechat](https://github.com/overtrue/laravel-wechat)

## Contributors

This project exists thanks to all the people who contribute. [[Contribute](CONTRIBUTING.md)].
<a href="https://github.com/overtrue/wechat/graphs/contributors"><img src="https://opencollective.com/wechat/contributors.svg?width=890" /></a>


## PHP 扩展包开发

> 想知道如何从零开始构建 PHP 扩展包？
>
> 请关注我的实战课程，我会在此课程中分享一些扩展开发经验 —— [《PHP 扩展包实战教程 - 从入门到发布》](https://learnku.com/courses/creating-package)


## License

MIT


[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fovertrue%2Fwechat.svg?type=large)](https://app.fossa.io/projects/git%2Bgithub.com%2Fovertrue%2Fwechat?ref=badge_large)
