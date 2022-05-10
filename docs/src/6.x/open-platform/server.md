# 服务端

第三方平台的服务端推送和公众号一样，请参考：[公众号：服务端](../official-account/server.md)

## 第三方平台推送事件处理

公众号第三方平台推送的有四个事件：

> 如已经授权的公众号、小程序再次进行授权，而未修改已授权的权限的话，是没有相关事件推送的。

- 授权成功 `authorized`
- 授权更新 `updateauthorized`
- 授权取消 `unauthorized`
- VerifyTicket `component_verify_ticket`

SDK 默认会处理事件 `component_verify_ticket` ，并会缓存 `verify_ticket` 所以如果你暂时不需要处理其他事件，直接这样使用即可：

```php
$server = $app->getServer();

return $server->serve();
```

## 内置消息处理器

> _消息处理器详细说明见公众号开发 - 服务端一节_

### 处理授权成功事件

```php
$server->handleAuthorized(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```
### 处理授权更新事件

```php
$server->handleAuthorizeUpdated(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```
### 处理授权取消事件

```php
$server->handleUnauthorized(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```

### 处理 VerifyTicket 推送事件（已默认处理）

此推送已经默认处理（使用缓存存储和刷新），可以直接忽略。

> 注意：如果你自行处理了 VerifyTicket 推送，你必须同时设置 ComponentAccessToken 类，因为 ComponentAccessToken 依赖它。

```php
$server->handleVerifyTicketRefreshed(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```

## 其它事件处理

以上便捷方法都只处理了特定事件，其它状态，可以通过自定义事件处理中间件的形式处理：

```php
$server->with(function($message, \Closure $next) {
    // $message->event_type 事件类型
    return $next($message);
});
```

## 自助处理推送消息

你可以通过下面的方式获取来自微信服务器的推送消息：


```php
$message = $server->getRequestMessage(); // 原始消息
```

你也可以获取解密后的消息 <version-tag>6.5.0+</version-tag>

```php
$message = $server->getDecryptedMessage();
```

`$message` 为一个 `EasyWeChat\OpenPlatform\Message` 实例。

你可以在处理完逻辑后自行创建一个响应，当然，在不同的框架里，响应写法也不一样，请自行实现。
