# 服务端

企业微信第三方服务端推送和公众号一样，请参考：[公众号：服务端](../official-account/server.md)

## 第三方平台推送事件处理

企业微信第三方数据推送的有以下事件：

- suite_ticket 推送 `suite_ticket`
- 授权成功 `create_auth`
- 授权变更 `change_auth`
- 授权取消 `cancel_auth`
- 通讯录变更（Event） `change_contact`
  - ChangeType
    - 成员变更
      - 新增成员 `create_user`
      - 更新成员 `update_user`
      - 删除成员 `delete_user`
    - 部门变更
      - 新增部门 `create_party`
      - 更新部门 `update_party`
      - 删除部门 `delete_party`
    - 标签变更
      - 成员标签变更 `update_tag`
- 共享应用事件回调 `share_agent_change`

## 内置消息处理器

> _消息处理器详细说明见：公众号开发 - 服务端一节_

### 授权成功事件

```php
$server->handleAuthCreated(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```

### 授权变更事件

```php
$server->handleAuthChanged(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```

### 授权取消事件

```php
$server->handleAuthCancelled(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```

### 通讯录变更事件

```php
$server->handleContactChanged(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```

### 成员变更事件

```php
// 新增成员
$server->handleUserCreated(function($message, \Closure $next) {
    // ...
    return $next($message);
});

// 更新成员
$server->handleUserUpdated(function($message, \Closure $next) {
    // ...
    return $next($message);
});

// 删除成员
$server->handleUserDeleted(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```

### 部门变更事件

```php
// 新增部门
$server->handlePartyCreated(function($message, \Closure $next) {
    // ...
    return $next($message);
});

// 更新部门
$server->handlePartyUpdated(function($message, \Closure $next) {
    // ...
    return $next($message);
});

// 删除部门
$server->handlePartyDeleted(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```

### 成员标签变更事件

```php
$server->handleUserTagUpdated(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```

### 共享应用事件

```php
$server->handleShareAgentChanged(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```

### suite_ticket 推送事件

此推送已经默认处理（使用缓存存储和刷新），可以直接忽略。

> 注意：如果你自行处理了 SuiteTicket 推送，你必须同时设置 ProviderAccessToken 类，因为 ProviderAccessToken 依赖它。

```php
$server->handleSuiteTicketRefreshed(callable | string $handler);
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

`$message` 为一个 `EasyWeChat\OpenWork\Message` 实例。

你可以在处理完逻辑后自行创建一个响应，当然，在不同的框架里，响应写法也不一样，请自行实现。
