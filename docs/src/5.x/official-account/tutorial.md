# 快速开始

在我们已经安装完成后，即可很快的开始使用它了，当然你还是有必要明白 PHP 基本知识，如命名空间等，我这里就不赘述了。

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

下面我们来配置一个基本的服务端，这里假设我们自己的服务器域名叫 `easywechat.com`，我们在服务器上准备这么一个文件`server.php`:

// server.php

```php
use EasyWeChat\Factory;

$config = [
    'app_id' => 'wx3cf0f39249eb0xxx',
    'secret' => 'f1c242f4f28f735d4687abb469072xxx',
    'token' => 'TestToken',
    'response_type' => 'array',
    //...
];

$app = Factory::officialAccount($config);

$response = $app->server->serve();

// 将响应输出
$response->send();exit; // Laravel 里请使用：return $response;

```

> :heart: 安全模式下请一定要配置 `aes_key`

一个服务端带验证功能的代码已经完成，当然没有对消息做处理，别着急，后面我们再讲。

我们先来分析上面的代码：

```php
// 引入我们的主项目工厂类。
use EasyWeChat\Factory;

// 一些配置
$config = [...];

// 使用配置来初始化一个公众号应用实例。
$app = Factory::officialAccount($config);

$response = $app->server->serve();

// 将响应输出
$response->send(); exit; // Laravel 里请使用：return $response;
```

最后这一行我有必要详细讲一下：

> 1.  我们的 `$app->server->serve()` 就是执行服务端业务了，那么它的返回值是一个 `Symfony\Component\HttpFoundation\Response` 实例。
> 2.  我这里是直接调用了它的 `send()` 方法，它就是直接输出（echo）了，我们在一些框架就不能直接输出了，那你就直接拿到 Response 实例后做相应的操作即可，比如 Laravel 里你就可以直接 `return $app->server->serve();`

OK, 有了上面的代码，那么请你按 **[微信官方的接入指引](http://mp.weixin.qq.com/wiki/)** 在公众号后台完成配置并启用，并相应修改上面的 `$config` 的相关配置。

> URL 就是我们的 `http://easywechat.com/server.php`，这里我是举例哦，你可不要填写我的域名。

这样，点击提交验证就 OK 了。

> :heart: 请一定要将微信后台的开发者模式 “**启用**” ！！！！！！看到红色 “**停用**” 才真正的是启用了。
> 最后，请不要用浏览器访问这个地址，它是给微信服务器访问的，不是给人访问的。

## 接收 & 回复用户消息

那服务端验证通过了，我们就来试一下接收消息吧。

> 在刚刚上面代码最后一行 `$app->server->serve()->send();` 前面，我们调用 `$app->server` 的 `push()` 方法来注册一个消息处理器，这里用到了 **[PHP 闭包](http://php.net/manual/zh/functions.anonymous.php)** 的知识，如果你不熟悉赶紧补课去。

```php
// ...

$app->server->push(function ($message) {
    return "您好！欢迎使用 EasyWeChat!";
});

$response = $app->server->serve();

// 将响应输出
$response->send(); // Laravel 里请使用：return $response;

```

> 注意：send() 方法里已经包含 echo 了，请不要再加 echo 在前面。

好吧，打开你的微信客户端，向你的公众号发送任意一条消息，你应该会收到回复：`您好！欢迎使用 EasyWeChat!`。

> 到了“你的公众号暂时无法提供服务” ？， 好，那检查一下你的日志吧，日志在哪儿？我们的配置里写了日志路径了(`__DIR__.'/wechat.log'`)。 没有这个文件？看看权限哦。

> avel 框架应用时，因 POST 请求默认会有 CSRF 验证，所以需要在 `App\Http\Middleware\VerifyCsrfToken` 的 `except` 数组中添加微信请求，否则会提示“你的公众号暂时无法提供服务”。

一个基本的服务端验证就完成了。

## 总结

1. 所有的应用服务都通过主入口 `EasyWeChat\Factory` 类来创建：

```php

// 公众号
$app = Factory::officialAccount($config);

// 小程序
$app = Factory::miniProgram($config);

// 开放平台
$app = Factory::openPlatform($config);

// 企业微信
$app = Factory::work($config);

// 企业微信开放平台
$app = Factory::openWork($config);

// 微信支付
$app = Factory::payment($config);

```

## 最后

希望你在使用本 SDK 的时候如果你发现 SDK 的不足，欢迎提交 PR 或者给我[提建议 & 报告问题](https://github.com/overtrue/wechat/issues)。
