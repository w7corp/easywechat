# 代授权方实现业务

> 授权方已经把公众号、小程序授权给你的开放平台第三方平台了，接下来的代授权方实现业务只需一行代码即可获得授权方实例。

## 实例化

```php
use EasyWeChat\Factory;

$config = [
    // ...
];

$openPlatform = Factory::openPlatform($config);
```

### 获取授权方实例

```php
// 代公众号实现业务
$officialAccount = $openPlatform->officialAccount(string $appId, string $refreshToken);
// 代小程序实现业务
$miniProgram = $openPlatform->miniProgram(string $appId, string $refreshToken);
```

> $appId 为授权方公众号 APPID，非开放平台第三方平台 APPID
>
> $refreshToken 为授权方的 refresh_token，可通过 [获取授权方授权信息](https://www.easywechat.com/docs/master/open-platform/index#heading-h2-2) 接口获得。

### 帮助授权方管理开放平台账号

```php
// 代公众号实现业务
$account = $officialAccount->account;
// 代小程序实现业务
$account = $miniProgram->account;

// 创建开放平台账号
// 并绑定公众号或小程序
$result = $account->create();

// 将公众号或小程序绑定到指定开放平台帐号下
$result = $account->bindTo($openAppId);

// 将公众号/小程序从开放平台帐号下解绑
$result = $account->unbindFrom($openAppid);

// 获取公众号/小程序所绑定的开放平台帐号
$result = $account->getBinding();
```

> 授权第三方平台注册的开放平台帐号只可用于获取用户 unionid 实现用户身份打通。
>
>  第三方平台不可操作（包括绑定/解绑）通过 open.weixin.qq.com 线上流程注册的开放平台帐号。
>
>  公众号只可将此权限集授权给一个第三方平台，授权互斥。

接下来的 API 调用等操作和公众号、小程序的开发一致，请移步到[公众号](https://www.easywechat.com/docs/master/official-account/index)或[小程序](https://www.easywechat.com/docs/master/mini-program/index)开发章节继续进行开发吧。

### 代码示例

```php
// 假设你的公众号消息与事件接收 URL 为：https://easywechat.com/$APPID$/callback ...

Route::post('{appId}/callback', function ($appId) {
    // ...
    $officialAccount = $openPlatform->officialAccount($appId);
    $server = $officialAccount->server; // ❗️❗️  这里的 server 为授权方的 server，而不是开放平台的 server，请注意！！！

    $server->push(function () {
        return 'Welcome!';
    });

    return $server->serve();
});

// 调用授权方业务例子
Route::get('how-to-use', function () {
    $officialAccount = $openPlatform->officialAccount('已授权的公众号 APPID', 'Refresh-token');
    // 获取用户列表：
    $officialAccount->user->list();

    $miniProgram = $openPlatform->miniProgram('已授权的小程序 APPID', 'Refresh-token');
    // 根据 code 获取 session
    $miniProgram->auth->session('js-code');
    // 其他同理
});
```
