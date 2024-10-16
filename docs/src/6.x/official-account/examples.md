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


<details>
    <summary>Hyperf 服务端验证消息</summary>
  
  ##### 方法一：
  * 安装包，composer require limingxinleo/easywechat-classmap，
  * 在授权回调地址中使用：
  ```php
  <?php

namespace app\controller;

use EasyWeChat\OfficialAccount\Application;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use Hyperf\Context\ApplicationContext;

// 授权事件回调地址：http://easywechat.com/OfficialAccount/server

class OfficialAccount
{
    public function server(RequestInterface $request, ResponseInterface $response)
    {
        $app = new Application(config('wechat.defaults'));
        
        if (method_exists($app, 'setRequest')) {
            $app->setRequest($request);  //必须替换服务端请求
        }

        if (method_exists($app, 'setCache')) {
            $app->setCache(ApplicationContext::getContainer()->get(CacheInterface::class)  //可选，根据实际需求替换缓存
        }

        $server = $app->getServer();
        
        $server->with(function ($message, \Closure $next) {
            return '谢谢关注！';
            
            // 你的自定义逻辑
            // return $next($message);
        });
        
        return $server->serve();
    }
}
  ```

##### 方法二：
* 安装包，composer require pengxuxu/hyperf-easywechat6，包里已替换了服务端请求和缓存，并封装了公众号、微信支付、小程序等外观。
* 参照文档在授权回调地址和其他场景中直接使用。
</details>

