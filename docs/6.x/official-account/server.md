# 服务端

你可以通过 `$app->getServer()` 获取服务端模块，**服务端模块默认处理了服务端验证的逻辑**：

```php
use EasyWeChat\OfficialAccount\Application;

$config = [...];
$app = new Application($config);

$server = $app->getServer();
```

### 中间件模式

与 5.x 的设计类似，服务端使用中间件模式来依次调用开发者注册的中间件：

```php
$server->with(function($message, \Closure $next) {
    // 你的自定义逻辑
    return $next($message);
});

$response = $server->serve();
```

你可以注册多个中间件来处理不同的情况：

```php
$server
    ->with(function($message, \Closure $next) {
        // 你的自定义逻辑1
        return $next($message);
    })
    ->with(function($message, \Closure $next) {
        // 你的自定义逻辑2
        return $next($message);
    })
    ->with(function($message, \Closure $next) {
        // 你的自定义逻辑3
        return $next($message);
    });

$response = $server->serve();
```

### 回复消息

当你在中间件里不回复消息时，你将要传递消息给下一个中间件：

```php
function($message, \Closure $next) {
    // 你的自定义逻辑3
    return $next($message);
}
```

如果此时你需要返回消息给用户，你可以直接像下面这样回复消息内容：

```php
function($message, \Closure $next) {
    return '感谢你使用 EasyWeChat';
}
```

> 注意：回复消息后其他没运行的中间件将不再执行，所以请你将全局都需要执行的中间件优先提前注册。

其他类型的消息时，请直接参考 **[官方文档消息的 XML 结构](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Passive_user_reply_message.html)** 以数组形式返回即可。

需要省略 `ToUserName`、`FromUserName` 和 `CreateTime`，以回复图片消息为例:

```php
function($message, \Closure $next) {
    return [
        'MsgType' => 'image',
        'Image' => [
            'MediaId' => 'media_id',
        ],
    ];
}
```

#### 怎么发送多条消息？

服务端只能回复一条消息，如果你想在接收到消息时向用户发送多条消息，你可以调用 **[客服消息](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Service_Center_messages.html)** 接口来发送多条。

### 使用独立的中间件类

当然，中间件也支持多种类型，比如你可以使用一个独立的类作为中间件：

```php
class MyCustomHandler
{
    public function __invoke($message, \Closure $next)
    {
        if ($message->MsgType === 'text') {
            //...
        }

        return $next($message);
    }
}
```

注册中间件：

```php
$server->with(MyCustomHandler::class);

// 或者

$server->with(new MyCustomHandler());
```

### 使用 callable 类型中间件

中间件支持 **[`callable`](http://php.net/manual/zh/language.types.callable.php)** 类型的参数，所以你不一定要传入一个闭包（Closure），你可以选择传入一个函数名，一个 `[$class, $method]` 或者 `Foo::bar` 这样的类型。

```php
$server->with([$object, 'method']);
$server->with('ClassName::method');
```

## 注册指定消息类型的消息处理器

为了方便开发者处理消息推送，server 类内置了两个便捷方法：

### 处理普通消息

当普通微信用户向公众账号发消息时被调用，且匹配对应的事件类型：

```php
$server->addMessageListener('text', function() { ... });
```

**参数**

- 参数 1 为消息类型，也就是 message 中的 `MsgType` 字段，例如：`image`;
- 参数 2 是中间件，也就是上面讲到的多种类型的中间件。

### 处理事件消息

事件消息中间件仅在推送事件消息时被调用，且匹配对应的事件类型：

```php
$server->addEventListener('subscribe', function() { ... });
```

**参数**

- 参数 1 为事件类型，也就是 message 中的 `Event` 字段，例如：`subscribe`;
- 参数 2 是中间件，也就是上面讲到的多种类型的中间件。

关于回复消息的结构，可以查阅 **[消息](message.md)** 章节了解更多。

## 完整示例

以下示例完成了服务端验证，自定义中间件回复等逻辑：

```php
use EasyWeChat\OfficialAccount\Application;

$config = [...];
$app = new Application($config);

$server = $app->getServer();

$server->addEventListener('subscribe', function($message, \Closure $next) {
    return '感谢您关注 EasyWeChat!';
});

$response = $server->serve();

return $response;
```

> `$response` 是一个 [Psr\Http\Message\ResponseInterface](https://github.com/php-fig/http-message/blob/master/src/ResponseInterface.php) 实现，所以请自己决定如何适配您的框架。
