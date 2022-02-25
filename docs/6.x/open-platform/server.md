# 服务端

第三方平台的服务端推送和公众号一样，请参考：[公众号：服务端](../official-account/server.md)

## 第三方平台推送事件

公众号第三方平台推送的有四个事件：

> 如已经授权的公众号、小程序再次进行授权，而未修改已授权的权限的话，是没有相关事件推送的。

- 授权成功 `authorized`
- 授权更新 `updateauthorized`
- 授权取消 `unauthorized`
- VerifyTicket `component_verify_ticket`

SDK 默认会处理事件 `component_verify_ticket` ，并会缓存 `verify_ticket` 所以如果你暂时不需要处理其他事件，直接这样使用即可：

```php
$server = $app->getServer();

$response = $server->serve();

return $response;
```

## 自定义消息处理器

> _消息处理器详细说明见公众号开发 - 服务端一节_

```php
// 处理授权成功事件
$server->handleAuthorized(callable | string $handler);

// 处理授权更新事件
$server->handleAuthorizeUpdated(callable | string $handler);

// 处理授权取消事件
$server->handleUnauthorized(callable | string $handler);

// 处理 VerifyTicket 推送事件（已默认处理）
$server->handleVerifyTicketRefreshed(callable | string $handler);
```

> 注意：如果你自行处理了 VerifyTicket 推送，你必须同时设置 ComponentAccessToken 类，因为 ComponentAccessToken 依赖它。

### 示例（Laravel 框架）

> 类路由关闭 csrf 验证。

```php
// 假设你的开放平台第三方平台设置的授权事件接收 URL 为: https://easywechat.com/open-platform （其他事件推送同样会推送到这个 URL）
Route::post('open-platform', function () {
    // $app 为你实例化的开放平台对象，此处省略实例化步骤
    return $app->server->serve(); // Done!
});

// 处理授权事件
Route::post('open-platform', function () {
    $server = $app->getServer();

    // 处理授权成功事件，其他事件同理
    $server->handleAuthorized(function ($message) {
        // $message 为微信推送的通知内容，不同事件不同内容，详看微信官方文档
        // 获取授权公众号 AppId： $message['AuthorizerAppid']
        // 获取 AuthCode：$message['AuthorizationCode']
        // 然后进行业务处理，如存数据库等...
    });

    return $server->serve();
});
```
