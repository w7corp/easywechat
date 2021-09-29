<h1 align="left"><a href="https://www.easywechat.com">EasyWeChat</a></h1>

📦 一个 PHP 微信开发 SDK。

[![Test Status](https://github.com/w7corp/easywechat/workflows/Test/badge.svg)](https://github.com/w7corp/easywechat/actions) 
[![Lint Status](https://github.com/w7corp/easywechat/workflows/Lint/badge.svg)](https://github.com/w7corp/easywechat/actions) 
[![Latest Stable Version](https://poser.pugx.org/w7corp/easywechat/v/stable.svg)](https://packagist.org/packages/w7corp/easywechat) 
[![Latest Unstable Version](https://poser.pugx.org/w7corp/easywechat/v/unstable.svg)](https://packagist.org/packages/w7corp/easywechat)
[![Total Downloads](https://poser.pugx.org/w7corp/easywechat/downloads)](https://packagist.org/packages/w7corp/easywechat) 
[![License](https://poser.pugx.org/w7corp/easywechat/license)](https://packagist.org/packages/w7corp/easywechat) 
[![huntr](https://cdn.huntr.dev/huntr_security_badge_mono.svg)](https://huntr.dev)

> 📣 **公告**
> 
>  为了更好的推进项目发展，保障项目更新迭代速度，EasyWeChat 正式并入微擎旗下，加上微擎团队的助力，将会为大家提供更强大更稳固更多元化的开源项目。
>
> - 微擎与 EasyWeChat 结合，基于微擎技术资源方面的优势，将积极发展 EasyWeChat 的开源社区，将为 EasyWeChat 开源项目注入巨大活力。
> - EasyWeChat 原作者 overtrue 将继续担任开源项目的核心开发者，继续参与项目的发展规划，共同打造更强大的开源生态社区。
> - 项目从 6.0 版本开始将修改包名为 `w7corp/easywechat`，5.x 及以下版本不受影响。

> 🚨 注意：请 PR 时往 5.x 提交，感谢您的贡献！


## Requirement

1. PHP >= 7.4
2. **[Composer](https://getcomposer.org/)**
3. openssl 拓展
4. fileinfo 拓展（素材管理模块需要用到）

## Installation

```shell
$ composer require "overtrue/wechat:^5.0" -vvv
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

[官网](https://www.easywechat.com)  · [教程](https://www.aliyundrive.com/s/6CwgtkiBqFV)  ·  [讨论](https://github.com/w7corp/easywechat/discussions)  ·  [微信公众平台](https://mp.weixin.qq.com/wiki)  ·  [WeChat Official](http://admin.wechat.com/wiki)

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
