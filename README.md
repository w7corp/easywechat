<p align="center">
<a href="https://easywechat.org/">
    <img src="http://7u2jwa.com1.z0.glb.clouddn.com/logo-20171121.png" height="300" alt="EasyWeChat Logo"/>
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
<a href="#backers"><img src="https://opencollective.com/wechat/backers/badge.svg" alt="Backers on Open Collective"></a>
<a href="#sponsors"><img src="https://opencollective.com/wechat/sponsors/badge.svg" alt="Sponsors on Open Collective"></a>
<a href="https://app.fossa.io/projects/git%2Bgithub.com%2Fovertrue%2Fwechat?ref=badge_shield" alt="FOSSA Status"><img src="https://app.fossa.io/api/projects/git%2Bgithub.com%2Fovertrue%2Fwechat.svg?type=shield"/></a>
</p>

</div>

<p align="center">
<img width="200" src="http://wx1.sinaimg.cn/mw690/82b94fb4gy1fgwafq32r0j20nw0nwter.jpg">
</p>

<p align="center">关注公众号我们一起聊聊代码怎么样？</p>

<p><img src="http://7u2jwa.com1.z0.glb.clouddn.com/QQ20171121-130611.jpg" alt="Features" /></p>

## Requirement

1. PHP >= 7.0
2. **[Composer](https://getcomposer.org/)**
3. openssl 拓展
4. fileinfo 拓展（素材管理模块需要用到）

## Installation

```shell
$ composer require "overtrue/wechat:~4.0" -vvv
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

[官网](https://www.easywechat.com)  · [教程](https://www.easywechat.com/tutorials)  ·  [讨论](https://www.easywechat.com/discussions)  ·  [微信公众平台](https://mp.weixin.qq.com/wiki)  ·  [WeChat Official](http://admin.wechat.com/wiki)

## Integration

[Laravel 5 拓展包: overtrue/laravel-wechat](https://github.com/overtrue/laravel-wechat)

## Contributors

This project exists thanks to all the people who contribute. [[Contribute](CONTRIBUTING.md)].
<a href="https://github.com/overtrue/wechat/graphs/contributors"><img src="https://opencollective.com/wechat/contributors.svg?width=890" /></a>



## License

MIT


[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fovertrue%2Fwechat.svg?type=large)](https://app.fossa.io/projects/git%2Bgithub.com%2Fovertrue%2Fwechat?ref=badge_large)
