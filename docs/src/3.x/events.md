# 事件


> 注意：3.0 起，所有服务端的入口（**消息与事件**）都已经合并为一个方法来处理：`setMessageHandler()`

### 在服务端接收用户端产生的事件

```php
<?php
use EasyWeChat\Foundation\Application;

// ...

$app = new Application($options);

$server = $app->server;

$server->setMessageHandler(function($message){
    // 注意，这里的 $message 不仅仅是用户发来的消息，也可能是事件
    // 当 $message->MsgType 为 event 时为事件
    if ($message->MsgType == 'event') {
        # code...
        switch ($message->Event) {
            case 'subscribe':
                # code...
                break;

            default:
                # code...
                break;
        }
    }
});

$response = $server->serve();

$response->send(); // Laravel 里请使用：return $response;
```

> 注意：`$response` 是一个对象，不要直接 echo.

更多请参考：[服务端](server.html)

关于事件类型请参考微信官方文档：http://mp.weixin.qq.com/wiki/
