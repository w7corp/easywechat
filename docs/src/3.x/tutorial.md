# 快速开始


在我们已经安装完成后，即可很快的开始使用它了，当然你还是有必要明白PHP基本知识，如命名空间等，我这里就不赘述了。

我们以完成服务器端验证与接收响应用户发送的消息为例来演示,首先你有必要了解一下微信交互的运行流程：

```
                                     +-----------------+                       +---------------+
    +----------+                     |                 |    POST/GET/PUT       |               |
    |          | ------------------> |                 | ------------------->  |               |
    |   user   |                     |  wechat server  |                       |  your server  |
    |          | < - - - - - - - - - |                 |                       |               |
    +----------+                     |                 | <- - - - - - - - - -  |               |
                                     +-----------------+                       +---------------+
```

那么我们要做的就是图中 **微信服务器把用户消息转到我们的自有服务器（虚线返回部分）** 后的处理过程。

## 服务端验证

在微信接入开始有一个 “服务器验证” 的过程，这一步呢，其实就是微信服务器向我们服务器发起一个请求（上图实线部分），传了一个名称为 `echostr` 的字符串过来，我们只需要原样返回就好了。

你也知道，微信后台只能填写一个服务器地址，所以 **服务器验证** 与 **消息的接收与回复**，都在这一个链接内完成交互。

考虑到这些，我已经把验证这一步给封装到 SDK 里了，你可以完全忽略这一步。

下面我们来配置一个基本的服务端，这里假设我们自己的服务器域名叫 `easywechat.org`，我们在服务器上准备这么一个文件`server.php`:

// server.php

```php
<?php

include __DIR__ . '/vendor/autoload.php'; // 引入 composer 入口文件

use EasyWeChat\Foundation\Application;

$options = [
    'debug'  => true,
    'app_id' => 'your-app-id',
    'secret' => 'you-secret',
    'token'  => 'easywechat',


    // 'aes_key' => null, // 可选

    'log' => [
        'level' => 'debug',
        'file'  => '/tmp/easywechat.log', // XXX: 绝对路径！！！！
    ],

    //...
];

$app = new Application($options);

$response = $app->server->serve();

// 将响应输出
$response->send(); // Laravel 里请使用：return $response;

```

> :heart: 安全模式下请一定要填写 `aes_key`

一个服务端带验证功能的代码已经完成，当然没有对消息做处理，别着急，后面我们再讲。

我们先来分析上面的代码：

```php
<?php

// 这行代码是引入 `composer` 的入口文件，这样我们的类才能正常加载。
include __DIR__ . '/vendor/autoload.php';

// 引入我们的主项目的入口类。
use EasyWeChat\Foundation\Application;

// 一些配置
$options = [...];

// 使用配置来初始化一个项目。
$app = new Application($options);

$response = $app->server->serve();

// 将响应输出
$response->send(); // Laravel 里请使用：return $response;
```

最后这一行我有必要详细讲一下：


>1. 我们的 `$app->server->serve()` 就是执行服务端业务了，那么它的返回值呢，是一个 `Symfony\Component\HttpFoundation\Response` 实例。
>2. 我这里是直接调用了它的 `send()` 方法，它就是直接输出了，我们在一些框架就不能直接输出了，那你就直接拿到 Response 实例后做相应的操作即可，比如 Laravel 里你就可以直接 `return $app->server->serve();`


OK, 有了上面的代码，那么请你按 **[微信官方的接入指引](http://mp.weixin.qq.com/wiki/17/2d4265491f12608cd170a95559800f2d.html)** 操作，并相应修改上面的 `$options` 的配置。

> URL 就是我们的 `http://easywechat.org/server.php`，这里我是举例哦，你可不要填写我的域名。

这样，点击提交验证就OK了。

> :heart: 请一定要将微信后台的开发者模式 “**启用**” ！！！！！！看到红色 “**停用**” 才真正的是启用了。


## 接收 & 回复用户消息

那服务端验证通过了，我们就来试一下接收消息吧。

> 在刚刚上面代码最后一行 `$app->server->serve()->send();` 前面，我们调用 `$app->server` 的 `setMessageHandler()` 方法来注册一个消息处理函数，这里用到了 **[PHP 闭包](http://php.net/manual/zh/functions.anonymous.php)** 的知识，如果你不熟悉赶紧补课去。

```php
// ...

$server->setMessageHandler(function ($message) {
    return "您好！欢迎关注我!";
});

$response = $app->server->serve();

// 将响应输出
$response->send(); // Laravel 里请使用：return $response;

```

> 注意：send() 方法里已经包含 echo 了，请不要再加 echo 在前面。

好吧，打开你的微信客户端，向你的公众号发送任意一条消息，你应该会收到回复：`您好！欢迎关注我!`。

> 没有收到回复？看到了“你的公众号暂时无法提供服务” ？， 好，那检查一下你的日志吧，日志在哪儿？我们的配置里写了日志路径了(`'/tmp/easywechat.log'`)。 没有这个文件？看看权限哦。

一个基本的服务端验证就完成了。

## 总结

1. 所有的服务都通过主入口 `EasyWeChat\Foundation\Application` 类来获取：

 ```php
 $app = new Application($options);

 // services...
 $server = $app->server;
 $user   = $app->user;
 $oauth  = $app->oauth;

 // ... js/menu/staff/material/qrcode/notice/stats...

 ```

2. 所有的 API 返回值均为 [`EasyWeChat\Support\Collection`](https://github.com/EasyWeChat/support/blob/master/src/Collection.php) 类，这个类是个什么东西呢？

 它实现了一些 **[PHP预定义接口](http://php.net/manual/zh/reserved.interfaces.php)**，比如：[`ArrayAccess`](http://php.net/manual/zh/class.arrayaccess.php)、[`Serializable`](http://php.net/manual/zh/class.serializable.php) 等。

 有啥好处呢？它让我们操作起返回值来更方便，比如：

 ```php
 $userService = $app->user; // 用户API

 $user = $userService->get($openId);

 // $user 便是一个 EasyWeChat\Support\Collection 实例
 $user['nickname'];
 $user->nickname;
 $user->get('nickname');

 //...
 ```

 还有这些方便的操作：检查是否存在某个属性 `$user->has('email')`、元素个数 `$user->count()`，还有返回数组 `$user->toArray()` ，生成 JSON `$user->toJSON()` 等。


 ## 最后

 希望你在使用本 SDK 的时候能忘记微信官方给你的痛苦，同时如果你发现 SDK 的不足，欢迎提交 PR 或者给我[提建议 & 报告问题](https://github.com/overtrue/wechat/issues)。

 祝你生活愉快！
