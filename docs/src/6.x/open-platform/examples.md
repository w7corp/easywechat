# 示例

> 👏🏻 欢迎点击本页下方 "帮助我们改善此页面！" 链接参与贡献更多的使用示例！


<details>
  <summary>Laravel 开放平台处理推送消息</summary>
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
</details>

<details>
  <summary>Laravel 开放平台代公众号处理回调事件</summary>

```php
// 代公众号处理回调事件
Route::any('callback/{appid}', function ($appid) {
    // $app 为你实例化的开放平台对象，此处省略实例化步骤
    // $token 为授权后你缓存的 authorizer_access_token，此处省略获取步骤
    $server = $app->getOfficialAccount(new AuthorizerAccessToken($appid, $token))->getServer();

    $server->addMessageListener('text', function ($message) {
        return sprintf("你对overtrue说：“%s”", $message->Content);
    });

    return $server->serve();
});
```
</details>


<!--
<details>
    <summary>标题</summary>
内容
</details>
-->
