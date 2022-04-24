# 示例

> 👏🏻 欢迎点击本页下方 "帮助我们改善此页面！" 链接参与贡献更多的使用示例！

<details>
  <summary>Laravel 开放平台处理推送消息</summary>

> 注意：对应路由需要关闭 csrf 验证。

假设你的开放平台第三方平台设置的授权事件接收 URL 为: https://easywechat.com/open-platform （其他事件推送同样会推送到这个 URL）

```php
// routes/web.php
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
    <summary>Laravel Octane(swoole) 开放平台处理推送消息</summary>

```php
// routes/web.php

use EasyWeChat\OpenPlatform\Application;

// 授权事件回调地址：http://yourdomain.com/open-platform/server
Route::post('open-platform/server', function () {
        $config = config('wechatv6.open_platform');
        $app = new Application($config);

        // 兼容octane
        $app->setRequestFromSymfonyRequest(request());

        $server = $app->getServer();
        return $server->serve();
});
```
</details>

<details>
    <summary>webman 开放平台处理推送消息</summary>

```php
namespace app\controller;

use EasyWeChat\OpenPlatform\Application;
use support\Request;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

// 授权事件回调地址：http://yourdomain.com/openPlatform/server

class OpenPlatform
{
    public function server(Request $request)
    {
        $config = config('wechatv6.open_platform');
        $app = new Application($config);
        $symfony_request = new SymfonyRequest($request->get(), $request->post(), [], $request->cookie(), [], [], $request->rawBody());
        $symfony_request->headers = new HeaderBag($request->header());
        $app->setRequestFromSymfonyRequest($symfony_request);
        $server = $app->getServer();
        $response = $server->serve();
        return $response->getBody()->getContents();
    }
}
```
</details>


<details>
  <summary>Laravel 开放平台PC版预授权<version-tag>6.3.0+</version-tag></summary>

官方文档： https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/2.0/api/Before_Develop/Authorization_Process_Technical_Description.html

用例：

```php
// routes/web.php

// 授权落地页
Route::any('open-platform/auth', function(){
        $auth_code = request()->get('auth_code');
        // 完成授权写入数据库的逻辑省略。。。
})->name('open_platform.auth');

// 授权跳转页
Route::any('open-platform/preauth', function(){
      // $app 为你实例化的开放平台对象，此处省略实例化步骤
      $options=[
            //1 表示手机端仅展示公众号；2 表示仅展示小程序，3 表示公众号和小程序都展示。如果为未指定，则默认小程序和公众号都展示。
            // 'auth_type' => '',

            // 指定的权限集id列表，如果不指定，则默认拉取当前第三方账号已经全网发布的权限集列表。
            // 'category_id_list' => '',
      ];

      $url = $app->createPreAuthorizationUrl(route('open_platform.auth'), $options);

      return response("<script>window.location.href='$url';</script>")->header('Content-Type', 'text/html');
});
```

</details>

<details>
  <summary>Laravel 开放平台代公众号/小程序代调用示例<version-tag>6.3.0+</version-tag></summary>

路由配置：

```php
// routes/web.php
// 例如：https://yourdomain.com/open-platform/miniapp/get-phone-number/wx123212312313abc

Route::any('open-platform/miniapp/get-phone-number/{appid}', 'OpenPlatformController@getPhoneNumber');
Route::any('open-platform/officialAccount/get-user-list/{appid}', 'OpenPlatformController@getUsers');
```

对应控制器：`app/Http/Controllers/OpenPlatformController`：

```php
use App\Http\Controllers\Controller;

class OpenPlatformController extends Controller
{
    public function mini(string $appid): \EasyWeChat\MiniApp\Application
    {
        $refreshToken = '授权后在缓存或数据库获取';

        // $app 为你实例化的开放平台对象，此处省略实例化步骤
        return $app->getMiniAppWithRefreshToken($appid, $refreshToken);
    }

    public function officialAccount(string $appid): \EasyWeChat\OfficialAccount\Application
    {
        $refreshToken = '授权后在缓存或数据库获取';

        // $app 为你实例化的开放平台对象，此处省略实例化步骤
        return $app->getOfficialAccountWithRefreshToken($appid, $refreshToken);
    }

    public function getUsers(string $appid)
    {
        return $this->officialAccount($appid)
                    ->getClient()
                    ->get('cgi-bin/users/list')
                    ->toArray();
    }

    public function getPhoneNumber(string $appid)
    {
        $data = [
          'code' => (string) request()->get('code'),
        ];

        return $this->mini($appid)
                    ->getClient()
                    ->postJson('wxa/business/getuserphonenumber', $data)
                    ->toArray();
    }
}
```

</details>

<details>
  <summary>Laravel 开放平台代公众号处理回调事件</summary>

```php
// 代公众号处理回调事件
Route::any('callback/{appid}', function ($appId) {
    // $app 为你实例化的开放平台对象，此处省略实例化步骤
    // $refreshToken 为授权后你缓存或数据库中的 authorizer_refresh_token，此处省略获取步骤

    $refreshToken = '你已缓存或数据库中的 authorizer_refresh_token';

    $server = $app->getOfficialAccountWithRefreshToken($appId, $refreshToken)->getServer();

    $server->addMessageListener('text', function ($message) {
        return sprintf("你对 overtrue 说：“%s”", $message->Content);
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
