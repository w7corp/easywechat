# Wechat

可能是目前最优雅的微信 SDK 了。[Laravel 5 版: overtrue/laravel-wechat](https://github.com/overtrue/laravel-wechat)

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
  composer require "overtrue/wechat:dev-master"
  ```

2. 手动安装

  下载 [最新版zip包](https://github.com/overtrue/wechat/archive/master.zip)  或者下载指定版本：https://github.com/overtrue/wechat/releases 。

  然后引入根目录的autoload.php即可：

  ```php
  <?php

  require "wechat/autoload.php"; // 路径请修改为你具体的实际路径

  ...
  ```

## 使用

基本使用

```php
<?php

use Overtrue\Wechat\Server;

$appId          = 'wx3cf0f39249eb0e60';
$token          = 'hellotest';
$encodingAESKey = 'EJThPazwzO4k1cyXJnwQtL60zBdhWvFaHb4emv0dLVN';

$server = new Server($appId, $token, $encodingAESKey);

$server->on('message', function($message){
    return "您好！欢迎关注 overtrue!";
});

// 您可以直接echo 或者返回给框架
echo $server->serve();
```

## 文档

[Wiki](https://github.com/overtrue/wechat/wiki)

> 强烈建议看懂微信文档后再来使用本 SDK。

## TODO

- [x] 用户
- [x] 用户组
- [x] 客服、多客服
- [x] 监听事件与消息
- [x] 基本消息类型
- [x] 图文消息
- [ ] 模板消息
- [ ] 群发消息
- [x] 自定义菜单
- [x] 素材管理
- [x] 门店管理
- [ ] 卡券管理
- [x] 网页授权
- [x] JSSDK
- [x] 二维码
- [x] 短链接

## 贡献代码

非常欢迎大家贡献代码共同完善本项目，让更多微信开发者早日摆脱苦恼，但请遵循[PSR标准](https://github.com/php-fig/fig-standards/blob/master/accepted/) 谢谢！

## License

MIT
