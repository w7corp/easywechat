# 服务端

## 第三方平台推送事件

公众号第三方平台推送的有四个事件：

> 如已经授权的公众号、小程序再次进行授权，而未修改已授权的权限的话，是没有相关事件推送的。

​	授权成功 `authorized`

​	授权更新 `updateauthorized`

​	授权取消 `unauthorized`

​	VerifyTicket  `component_verify_ticket`

SDK 默认会处理事件 `component_verify_ticket` ，并会缓存 `verify_ticket` 所以如果你暂时不需要处理其他事件，直接这样使用即可：

```php
$server = $openPlatform->server;

return $server->serve();
```

## 自定义消息处理器

> *消息处理器详细说明见公众号开发 - 服务端一节*

```php
use EasyWeChat\OpenPlatform\Server\Guard;

$server = $openPlatform->server;

// 处理授权成功事件
$server->push(function ($message) {
    // ...
}, Guard::EVENT_AUTHORIZED);

// 处理授权更新事件
$server->push(function ($message) {
    // ...
}, Guard::EVENT_UPDATE_AUTHORIZED);

// 处理授权取消事件
$server->push(function ($message) {
    // ...
}, Guard::EVENT_UNAUTHORIZED);
```

### 示例（Laravel 框架）

```php
// 假设你的开放平台第三方平台设置的授权事件接收 URL 为: https://easywechat.com/open-platform （其他事件推送同样会推送到这个 URL）
Route::post('open-platform', function () { // 关闭 CSRF
    // $openPlatform 为你实例化的开放平台对象，此处省略实例化步骤
    return $openPlatform->server->serve(); // Done!
});

// 处理事件
use EasyWeChat\OpenPlatform\Server\Guard;
Route::post('open-platform', function () {
    $server = $openPlatform->server;
    // 处理授权成功事件，其他事件同理
    $server->push(function ($message) {
        // $message 为微信推送的通知内容，不同事件不同内容，详看微信官方文档
        // 获取授权公众号 AppId： $message['AuthorizerAppid']
        // 获取 AuthCode：$message['AuthorizationCode']
        // 然后进行业务处理，如存数据库等...
    }, Guard::EVENT_AUTHORIZED);

    return $server->serve();
});
```
