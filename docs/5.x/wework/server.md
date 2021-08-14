## 服务端

我们在企业微信应用开启接收消息的功能，将设置页面的 token 与 aes key 配置到 agents 下对应的应用内：

```php
$config = [
    'corp_id' => 'xxxxxxxxxxxxxxxxx',

    'agent_id' => 100022,
    'secret'   => 'xxxxxxxxxx',

    // server config
    'token' => 'xxxxxxxxx',
    'aes_key' => 'xxxxxxxxxxxxxxxxxx',

    //...
];

$app = Factory::work($config);
```

接着配置服务端与公众号的服务端用法一样：

```php
$app->server->push(function($message){
   // $message['FromUserName'] // 消息来源
   // $message['MsgType'] // 消息类型：event ....
    
    return 'Hello easywechat.';
});

$response = $app->server->serve();

$response->send();
```

`$response` 为 `Symfony\Component\HttpFoundation\Response` 实例，根据你的框架情况来决定如何处理响应。
