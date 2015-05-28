# Wechat

May be the most beautiful Wechat SDK till now.[Laravel 5 extension package: overtrue/laravel-wechat](https://github.com/overtrue/laravel-wechat)

We discuss @ QQ Group：319502940. Welcome to join us! ^^

[![Build Status](https://travis-ci.org/overtrue/wechat.svg?branch=master)](https://travis-ci.org/overtrue/wechat)
[![Latest Stable Version](https://poser.pugx.org/overtrue/wechat/v/stable.svg)](https://packagist.org/packages/overtrue/wechat)
[![Latest Unstable Version](https://poser.pugx.org/overtrue/wechat/v/unstable.svg)](https://packagist.org/packages/overtrue/wechat)
[![Build Status](https://scrutinizer-ci.com/g/overtrue/wechat/badges/build.png?b=master)](https://scrutinizer-ci.com/g/overtrue/wechat/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/overtrue/wechat/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/overtrue/wechat/?branch=master)
[![Total Downloads](https://poser.pugx.org/overtrue/wechat/downloads)](https://packagist.org/packages/overtrue/wechat)
[![License](https://poser.pugx.org/overtrue/wechat/license)](https://packagist.org/packages/overtrue/wechat)

There are lots of Wechat SDKs around world, but no one can satisfied me, cause I want a SDK has the advantage below:

 - Well organized & clear named classes & files;
 - Hide the detail things developer needn't know; 
 - Beautiful method usage；
 - Customized cached method；
 - Meet [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md) standard；
 - Highly abstracted Message class, avoid to merge json & xml;

## Installation

Requirement：PHP >= 5.3.0

1. use [composer](https://getcomposer.org/)

  ```shell
  composer require "overtrue/wechat:~2.0.*"
  ```

2. Manually

  Download [zip](https://github.com/overtrue/wechat/archive/master.zip)  or use the version you want：https://github.com/overtrue/wechat/releases 

  then use the autoload.php file in the package root folder：

  ```php
  <?php

  require "wechat/autoload.php"; // 路径请修改为你具体的实际路径

  ...
  ```

## Usage

Basic Usage（Server Side）:

```php
<?php

use Overtrue\Wechat\Server;

$appId          = 'wx3cf0f39249eb0e60';

$server = new Server($appId);

$server->on('message', function($message){
    return "Hello! Welcome to see overtrue!";
});

// You can either echo or return 
echo $server->serve();
```
See more usage in Wiki.

## Documentation

[Wiki](https://github.com/overtrue/wechat/wiki)

> I highly recommended that you should read the Wechat's official documents first.

## Features

- [x] [Message Listener](https://github.com/overtrue/wechat/wiki/接收消息与回复)
- [x] [Event Listener](https://github.com/overtrue/wechat/wiki/监听微信事件)
- [x] [Basic Message](https://github.com/overtrue/wechat/wiki/消息的使用)
- [x] [Picture and Text Message](https://github.com/overtrue/wechat/wiki/消息的使用)
- [x] [Template Message](https://github.com/overtrue/wechat/wiki/模板消息)  
- <del>[ ] Message Mass Sending（Due to the limitation of the official, it can't be used）</del>
- [x] [User and Group](https://github.com/overtrue/wechat/wiki/用户与用户组管理)
- [x] [Customer Service and Message Send Back](https://github.com/overtrue/wechat/wiki/客服管理与发送消息)
- [x] [MultiCustomer Service and Message Relocate](https://github.com/overtrue/wechat/wiki/多客服与消息转发)
- [x] [Web Authorization](https://github.com/overtrue/wechat/wiki/网页授权)
- [x] [Customized Menu](https://github.com/overtrue/wechat/wiki/自定义菜单)
- [x] [Material Management](https://github.com/overtrue/wechat/wiki/素材管理) 
- [x] [Store Management](https://github.com/overtrue/wechat/wiki/门店管理) 
- [x] [Card Management](https://github.com/overtrue/wechat/wiki/卡券)  
- [x] [JSSDK](https://github.com/overtrue/wechat/wiki/JSSDK)  
- [x] [Semantic Understanding](https://github.com/overtrue/wechat/wiki/语义理解服务)  
- [x] [Data statistics](https://github.com/overtrue/wechat/wiki/数据统计查询服务)  
- [x] [QR Code](https://github.com/overtrue/wechat/wiki/二维码)  
- [x] [Short Links](https://github.com/overtrue/wechat/wiki/短链接)  

## Contributing

PR is warmly welcomed，please follow the [PSR standard](https://github.com/php-fig/fig-standards/blob/master/accepted/) Thank you!

## License

MIT
