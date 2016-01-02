# Wechat

[English](README_EN.md)

可能是目前最优雅的微信公众平台 SDK 了。[Laravel 5 拓展包: overtrue/laravel-wechat](https://github.com/overtrue/laravel-wechat)

> 3.0 开发中，目前大部分模块已经完成，希望您能帮忙测试为谢！[3.0](https://github.com/overtrue/wechat/tree/3.0)

SDK QQ群：`319502940`

微信开发者交流群：`9179779` （这不是微信群，是名称叫“微信开发者交流群” 的QQ群，微信上聊技术？你逗我？）

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
 - 高度抽象的消息类，免去各种拼json与xml的痛苦。

## 安装

环境要求：PHP >= 5.3.0

1. 使用 [composer](https://getcomposer.org/)

  ```shell
  composer require "overtrue/wechat:2.1.*"
  ```

2. 手动安装

  下载 [2.1版zip包](https://github.com/overtrue/wechat/archive/2.1.zip)  或者下载指定版本：https://github.com/overtrue/wechat/releases 。

  然后引入根目录的autoload.php即可：

  ```php
  <?php

  require "wechat/autoload.php"; // 路径请修改为你具体的实际路径

  ...
  ```

3. 确认你没装 laravel-debugbar!!!!

## 使用

基本使用（以服务端为例）:

```php
<?php

use Overtrue\Wechat\Server;

$appId = 'wx3cf0f39249eb0e60';
$token = "overtrue";

$server = new Server($appId, $token);

$server->on('message', function($message){
    return "您好！欢迎关注 overtrue!";
});

// 您可以直接echo 或者返回给框架
echo $server->serve();
```

更多请参考文档。

:mega: 现在我们已经把用 2.0 API 写的一个基本例子开源了：[微易](https://github.com/vieasehub/viease)

## 文档

[Wiki](https://github.com/overtrue/wechat/wiki)

> 强烈建议看懂微信文档后再来使用本 SDK。

## Features

- [x] [监听消息](https://github.com/overtrue/wechat/wiki/接收消息与回复)
- [x] [监听事件](https://github.com/overtrue/wechat/wiki/监听微信事件)
- [x] [基本消息类型](https://github.com/overtrue/wechat/wiki/消息的使用)
- [x] [图文消息](https://github.com/overtrue/wechat/wiki/消息的使用)
- [x] [模板消息](https://github.com/overtrue/wechat/wiki/模板消息)
- [x] [群发消息](https://github.com/overtrue/wechat/wiki/群发消息) `2.1.28+`
- [x] [用户与用户组](https://github.com/overtrue/wechat/wiki/用户与用户组管理)
- [x] [客服与消息发送](https://github.com/overtrue/wechat/wiki/客服管理与发送消息)
- [x] [多客服与消息转发](https://github.com/overtrue/wechat/wiki/多客服与消息转发)
- [x] [网页授权](https://github.com/overtrue/wechat/wiki/网页授权)
- [x] [自定义菜单](https://github.com/overtrue/wechat/wiki/自定义菜单)
- [x] [素材管理](https://github.com/overtrue/wechat/wiki/素材管理)
- [x] [门店管理](https://github.com/overtrue/wechat/wiki/门店管理)
- [x] [卡券管理](https://github.com/overtrue/wechat/wiki/卡券)
- [x] [JSSDK](https://github.com/overtrue/wechat/wiki/JSSDK)
- [x] [语义理解](https://github.com/overtrue/wechat/wiki/语义理解服务)
- [x] [数据统计](https://github.com/overtrue/wechat/wiki/数据统计查询服务)
- [x] [二维码](https://github.com/overtrue/wechat/wiki/二维码)
- [x] [短链接](https://github.com/overtrue/wechat/wiki/短链接)
- [x] [微信支付](https://github.com/overtrue/wechat/wiki/微信支付)
- [ ] [微信小店]()

## 贡献代码

非常欢迎大家贡献代码共同完善本项目，烦请遵循 [PSR标准](https://github.com/php-fig/fig-standards/blob/master/accepted/) 谢谢！

## License

MIT
