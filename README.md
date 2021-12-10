<img align="right" width="100" src="https://user-images.githubusercontent.com/1472352/49656357-1e874080-fa78-11e8-80ea-69e2103345cf.png" alt="EasyWeChat Logo" expires="2021-08-13" />

<h1 align="left"><a href="https://www.easywechat.com">EasyWeChat</a></h1>

📦 一个 PHP 微信开发 SDK。

[![Test Status](https://github.com/w7corp/easywechat/workflows/Test/badge.svg)](https://github.com/w7corp/easywechat/actions) 
[![Lint Status](https://github.com/w7corp/easywechat/workflows/Lint/badge.svg)](https://github.com/w7corp/easywechat/actions) 
[![Latest Stable Version](https://poser.pugx.org/w7corp/easywechat/v/stable.svg)](https://packagist.org/packages/w7corp/easywechat) 
[![Latest Unstable Version](https://poser.pugx.org/w7corp/easywechat/v/unstable.svg)](https://packagist.org/packages/w7corp/easywechat)
[![Total Downloads](https://poser.pugx.org/w7corp/easywechat/downloads)](https://packagist.org/packages/w7corp/easywechat) 
[![License](https://poser.pugx.org/w7corp/easywechat/license)](https://packagist.org/packages/w7corp/easywechat) 


## Requirement

1. PHP >= 8
2. **[Composer](https://getcomposer.org/)**
3. openssl 拓展
4. fileinfo 拓展（素材管理模块需要用到）

## Installation

```shell
$ composer require "w7corp/easywechat:dev-develop" -vvv
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

    return "{$fromUser->nickname} 您好！欢迎关注!";
});

$server->serve()->send();
```

更多请参考 [https://www.easywechat.com/](https://www.easywechat.com/)。

## Documentation

[官网](https://www.easywechat.com)  · [教程](https://www.aliyundrive.com/s/6CwgtkiBqFV)  ·  [讨论](https://github.com/w7corp/easywechat/discussions)  ·  [微信公众平台](https://mp.weixin.qq.com/wiki)  ·  [WeChat Official](http://admin.wechat.com/wiki)  ·  [更新策略](https://github.com/w7corp/easywechat/security/policy)

## Contributors

This project exists thanks to all the people who contribute. [[Contribute](CONTRIBUTING.md)].
<a href="https://github.com/w7corp/easywechat/graphs/contributors"><img src="https://opencollective.com/wechat/contributors.svg?width=890" /></a>


## License

MIT
