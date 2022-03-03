# 网页授权


## 关于 OAuth2.0

OAuth是一个关于授权（authorization）的开放网络标准，在全世界得到广泛应用，目前的版本是2.0版。

```

     +--------+                               +---------------+
     |        |--(A)- Authorization Request ->|   Resource    |
     |        |                               |     Owner     |
     |        |<-(B)-- Authorization Grant ---|               |
     |        |                               +---------------+
     |        |
     |        |                               +---------------+
     |        |--(C)-- Authorization Grant -->| Authorization |
     | Client |                               |     Server    |
     |        |<-(D)----- Access Token -------|               |
     |        |                               +---------------+
     |        |
     |        |                               +---------------+
     |        |--(E)----- Access Token ------>|    Resource   |
     |        |                               |     Server    |
     |        |<-(F)--- Protected Resource ---|               |
     +--------+                               +---------------+
                      OAuth 授权流程
```
> 摘自：[RFC 6749](https://datatracker.ietf.org/doc/rfc6749/?include_text=1)

步骤解释：

    （A）用户打开客户端以后，客户端要求用户给予授权。
    （B）用户同意给予客户端授权。
    （C）客户端使用上一步获得的授权，向认证服务器申请令牌。
    （D）认证服务器对客户端进行认证以后，确认无误，同意发放令牌。
    （E）客户端使用令牌，向资源服务器申请获取资源。
    （F）资源服务器确认令牌无误，同意向客户端开放资源。

关于 OAuth 协议我们就简单了解到这里，如果还有不熟悉的同学，请 [Google 相关资料](https://www.google.com.hk/?gws_rd=ssl#safe=strict&q=OAuth2)

## 微信 OAuth

在微信里的 OAuth 其实有两种：[公众平台网页授权获取用户信息](http://mp.weixin.qq.com/wiki/9/01f711493b5a02f24b04365ac5d8fd95.html)、[开放平台网页登录](https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419316505&token=&lang=zh_CN)。

它们的区别有两处，授权地址不同，`scope` 不同。

- **公众平台网页授权获取用户信息**

  **授权 URL**: `https://open.weixin.qq.com/connect/oauth2/authorize`
  **Scopes**: `snsapi_base` 与 `snsapi_userinfo`

- **开放平台网页登录**

  **授权 URL**: `https://open.weixin.qq.com/connect/qrconnect`
  **Scopes**: `snsapi_login`

他们的逻辑都一样：

1. 用户尝试访问一个我们的业务页面，例如: `/user/profile`
2. 如果用户已经登录，则正常显示该页面
2. 系统检查当前访问的用户并未登录（从 session 或者其它方式检查），则跳转到**跳转到微信授权服务器**（上面的两种中一种**授权 URL**），并告知微信授权服务器我的**回调URL（redirect_uri=callback.php)**，此时用户看到蓝色的授权确认页面（`scope` 为 `snsapi_base` 时不显示）
4. 用户点击确定完成授权，浏览器跳转到**回调URL**: `callback.php` 并带上 `code`： `?code=CODE&state=STATE`。
5. 在 `callback.php` 中得到 `code` 后，通过 `code` 再次向微信服务器请求得到 **网页授权 access_token** 与 `openid`
6. 你可以选择拿 `openid` 去请求 API 得到用户信息（可选）
7. 将用户信息写入 SESSION。
8. 跳转到第 3 步写入的 `target_url` 页面（`/user/profile`）。

> 看懵了？没事，使用 SDK，你不用管这么多。:smile:
>
> 注意，上面的第3步：redirect_uri=callback.php实际上我们会在 `callback.php` 后面还会带上授权目标页面 `user/profile`，所以完整的 `redirect_uri` 应该是下面的这样的PHP去拼出来：`'redirect_uri='.urlencode('callback.php?target=user/profile')`
> 结果：redirect_uri=callback.php%3Ftarget%3Duser%2Fprofile

## 逻辑组成

从上面我们所描述的授权流程来看，我们至少有3个页面：

1. **业务页面**，也就是需要授权才能访问的页面。
2. **发起授权页**，此页面其实可以省略，可以做成一个中间件，全局检查未登录就发起授权。
3. **授权回调页**，接收用户授权后的状态，并获取用户信息，写入用户会话状态（SESSION）。

## 开始之前

在开始之前请一定要记住，先登录公众号后台，找到**边栏 “开发”** 模块下的 **“接口权限”**，点击 **“网页授权获取用户基本信息”** 后面的修改，添加你的网页授权域名。

> 如果你的授权地址为：`http://www.abc.com/xxxxx`，那么请填写 `www.abc.com`，也就是说请填写与网址匹配的域名，前者如果填写 `abc.com` 是通过不了的。

## SDK 中 OAuth 模块的 API

  在 SDK 中，我们使用名称为 `oauth` 的模块来完成授权服务，我们主要用到以下两个 API：

### 发起授权

```php
$response = $app->oauth->scopes(['snsapi_userinfo'])
                          ->redirect();
```

当你的应用是分布式架构且没有会话保持的情况下，你需要自行设置请求对象以实现会话共享。比如在 [Laravel](http://laravel.com) 框架中支持Session储存在Redis中，那么需要这样：

```php
$response = $app->oauth->scopes(['snsapi_userinfo'])
                          ->setRequest($request)
                          ->redirect();

//回调后获取user时也要设置$request对象
//$user = $app->oauth->setRequest($request)->user();
```

它的返回值 `$response` 是一个 [Symfony\Component\HttpFoundation\RedirectResponse](http://api.symfony.com/3.0/Symfony/Component/HttpFoundation/RedirectResponse.html) 实例。

你可以选择在框架中做一些正确的响应，比如在 [Laravel](http://laravel.com) 框架中控制器方法是要求返回响应值的，那么你就直接:

```php
return $response;
```

在有的框架 (比如yii2) 中是直接 `echo` 或者 `$this->display()` 这种的时候，你就直接：

```php
$response->send(); // Laravel 里请使用：return $response;
```

### 获取已授权用户

```php
$user = $app->oauth->user();
// $user 可以用的方法:
// $user->getId();  // 对应微信的 OPENID
// $user->getNickname(); // 对应微信的 nickname
// $user->getName(); // 对应微信的 nickname
// $user->getAvatar(); // 头像网址
// $user->getOriginal(); // 原始API返回的结果
// $user->getToken(); // access_token， 比如用于地址共享时使用
```

返回的 `$user` 是 [Overtrue\Socialite\User](https://github.com/overtrue/socialite/blob/master/src/User.php) 对象，你可以从该对象拿到[更多的信息](https://github.com/overtrue/socialite#user-interface)。

> :pray: 注意：`$user` 里没有 `openid`， `$user->id` 便是 `openid`.
> 如果你想拿微信返回给你的原样的全部信息，请使用：$user->getOriginal();

当 `scope` 为 `snsapi_base` 时 `$oauth->user();` 对象里只有 `id`，没有其它信息。

## 网页授权实例

我们这里来用原生 PHP 写法举个例子，`oauth_callback` 是我们的授权回调URL (未urlencode编码的URL), `user/profile` 是我们需要授权才能访问的页面，它的 PHP 代码如下：

```php
// http://easywechat.org/user/profile
<?php

use EasyWeChat\Foundation\Application;

$config = [
  // ...
  'oauth' => [
      'scopes'   => ['snsapi_userinfo'],
      'callback' => '/oauth_callback',
  ],
  // ..
];

$app = new Application($config);
$oauth = $app->oauth;

// 未登录
if (empty($_SESSION['wechat_user'])) {

  $_SESSION['target_url'] = 'user/profile';

  return $oauth->redirect();
  // 这里不一定是return，如果你的框架action不是返回内容的话你就得使用
  // $oauth->redirect()->send();
}

// 已经登录过
$user = $_SESSION['wechat_user'];

// ...

```

授权回调页：

```php
// http://easywechat.org/oauth_callback
<?php

use EasyWeChat\Foundation\Application;

$config = [
  // ...
];

$app = new Application($config);
$oauth = $app->oauth;

// 获取 OAuth 授权结果用户信息
$user = $oauth->user();

$_SESSION['wechat_user'] = $user->toArray();

$targetUrl = empty($_SESSION['target_url']) ? '/' : $_SESSION['target_url'];

header('location:'. $targetUrl); // 跳转到 user/profile
```

上面的例子呢都是基于 `$_SESSION` 来保持会话的，在微信客户端中，你可以结合 COOKIE 来存储，但是有效期平台不一样时间也不一样，好像 Android 的失效会快一些，不过基本也够用了。


更多关于微信网页授权 API 请参考： http://mp.weixin.qq.com/wiki/
更多开放平台网页登录请参考：https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419316505&token=&lang=zh_CN
