# Wechat

可能是目前最优雅的微信 SDK 了

[![Build Status](https://travis-ci.org/overtrue/wechat.svg?branch=master)](https://travis-ci.org/overtrue/wechat)
[![Latest Stable Version](https://poser.pugx.org/overtrue/wechat/v/stable.svg)](https://packagist.org/packages/overtrue/wechat)
[![Latest Unstable Version](https://poser.pugx.org/overtrue/wechat/v/unstable.svg)](https://packagist.org/packages/overtrue/wechat)
[![Build Status](https://scrutinizer-ci.com/g/overtrue/wechat/badges/build.png?b=master)](https://scrutinizer-ci.com/g/overtrue/wechat/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/overtrue/wechat/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/overtrue/wechat/?branch=master)
[![Total Downloads](https://poser.pugx.org/overtrue/wechat/downloads.svg)](https://packagist.org/packages/overtrue/wechat)

网上充斥着各种微信 SDK，但是找了一圈，发现没有一个想用，因为没有满足本项目存在后的各种优点：

 - 命名不那么乱七八糟；
 - 隐藏开发者不需要关注的细节；
 - 方法使用更优雅，不再那么恶心的使用恶心的命名譬如：`getXML4Image...`；
 - 统一的错误处理，让你更方便的掌控异常；
 - 自定义缓存方式；
 - 符合 [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md) 标准，你可以各种方便的与你的框架集成；
 - 高度抽象的消息类，免去各种拼json与xml的痛苦。

## 安装

环境要求：PHP >= 5.3.0

1. 使用 [composer](https://getcomposer.org/):

  ```shell
  composer require overtrue/wechat
  ```

2. 手动安装

  下载 [最新版zip包](https://github.com/overtrue/wechat/archive/master.zip)  或者下载指定版本：https://github.com/overtrue/wechat/releases 。

  然后引入根目录的autoload.php即可：
  
  ```php
  <?php
  
  require "wechat/autoload.php"; // 路径请修改为你具体的实际路径
  
  use Overtrue\Wechat\Wechat;
  ...
  ```

## 使用

基本使用

```php
<?php

use Overtrue\Wechat\Wechat;

$options = [
    'appId'          => 'Your app id',
    'secret'         => 'Your secret'
    'token'          => 'Your token',
    'encodingAESKey' => 'Your encoding AES Key' // optional
];

$wechat = Wechat::make($options);

$server = $wechat->on('message', function($message){
    error_log("收到来自'{$message['FromUserName']}'的消息：{$message['Content']}");
});

$result = $wechat->serve();

// 您可以直接echo 或者返回给框架
echo $result;
```

## 文档

[Wiki](https://github.com/overtrue/wechat/wiki)

## TODO

- [x] 用户
- [x] 用户组
- [x] 客服
- [x] 监听事件与消息
- [x] 基本消息类型
- [x] 图文消息
- [ ] 群发消息
- [x] 自定义菜单 
- [x] Auth
- [ ] Ticket
- [ ] 二维码
- [ ] 短链接

## 贡献代码

欢迎大家贡献代码，但请遵循[PSR标准](https://github.com/php-fig/fig-standards/blob/master/accepted/) 谢谢！

## License

MIT
