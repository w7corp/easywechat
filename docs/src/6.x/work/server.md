# 服务端

企业微信服务端推送和公众号一样，请参考：[公众号：服务端](../official-account/server.md)

## 第三方平台推送事件

企业微信数据推送的有以下事件：

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
- 批量任务执行完成 `batch_job_result`

## 内置消息处理器

### 处理通讯录变更事件（包括成员变更、部门变更、成员标签变更）

```php
$server->handleContactChanged(function($message, \Closure $next) {
    // ...
    return $next($message);
});
```

### 处理任务执行完成事件

```php
$server->handleBatchJobsFinished(function($message, \Closure $next) {
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
