# 示例

> 👏🏻 欢迎点击本页下方 "帮助我们改善此页面！" 链接参与贡献更多的使用示例！

<details>
    <summary>webman 服务端验证消息</summary>

```php
<?php

namespace app\controller;

use EasyWeChat\OfficialAccount\Application;
use support\Request;
use support\Redis;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

// 授权事件回调地址：http://easywechat.com/OfficialAccount/server

class OfficialAccount
{
    public function server(Request $request)
    {
        $config = config('wechatv6.official_account');
        $app = new Application($config);
        $symfony_request = new SymfonyRequest($request->get(), $request->post(), [], $request->cookie(), [], [], $request->rawBody());
        $symfony_request->headers = new HeaderBag($request->header());
        $app->setRequestFromSymfonyRequest($symfony_request);//必须替换服务端请求
        //$app->setCache(new Psr16Cache(new RedisAdapter(Redis::connection()->client())));//根据需要替换缓存，access_token公众号的全局唯一接口调用凭据会使用该缓存存储
        $server = $app->getServer();
        $response = $server->serve();

        return response($response->getBody());
    }
}
```

</details>

<!--
<details>
    <summary>标题</summary>
内容
</details>
-->
