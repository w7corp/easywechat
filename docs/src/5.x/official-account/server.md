# 服务端

我们在入门小教程一节以服务端为例讲解了一个基本的消息的处理，这里就不再讲服务器验证的流程了，请直接参考前面的入门实例即可。

服务端的作用呢，在整个微信开发中主要是负责 **[接收用户发送过来的消息](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Receiving_standard_messages.html)**，还有 **[用户触发的一系列事件](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Receiving_event_pushes.html)**。

首先我们得理清消息与事件的回复逻辑，当你收到用户消息后（消息由微信服务器推送到你的服务器），在你对消息进行一些处理后，不管是选择回复一个消息还是什么不都回给用户，你也应该给微信服务器一个 “答复”，如果是选择回复一条消息，就直接返回一个消息xml就好，如果选择不作任何回复，你也得回复一个空字符串或者字符串 `SUCCESS`（不然用户就会看到 `该公众号暂时无法提供服务`）。

## 基本使用

在 SDK 中使用 `$app->server->push(callable $callback)` 来设置消息处理器：

```php
$app->server->push(function ($message) {
    // $message['FromUserName'] // 用户的 openid
    // $message['MsgType'] // 消息类型：event, text....
    return "您好！欢迎使用 EasyWeChat";
});

// 在 laravel 中：
$response = $app->server->serve();

// $response 为 `Symfony\Component\HttpFoundation\Response` 实例
// 对于需要直接输出响应的框架，或者原生 PHP 环境下
$response->send();

// 而 laravel 中直接返回即可：

return $response;
```

这里我们使用 `push` 传入了一个 **闭包（[Closure](http://php.net/manual/en/class.closure.php)）**，该闭包接收一个参数 `$message` 为消息对象（类型取决于你的配置中 `response_type`），你可以在全局消息处理器中对消息类型进行筛选：

```php
$app->server->push(function ($message) {
    switch ($message['MsgType']) {
        case 'event':
            return '收到事件消息';
            break;
        case 'text':
            return '收到文字消息';
            break;
        case 'image':
            return '收到图片消息';
            break;
        case 'voice':
            return '收到语音消息';
            break;
        case 'video':
            return '收到视频消息';
            break;
        case 'location':
            return '收到坐标消息';
            break;
        case 'link':
            return '收到链接消息';
            break;
        case 'file':
            return '收到文件消息';
        // ... 其它消息
        default:
            return '收到其它消息';
            break;
    }

    // ...
});
```

当然，因为这里 `push` 接收一个 [`callable`](http://php.net/manual/zh/language.types.callable.php) 的参数，所以你不一定要传入一个 Closure 闭包，你可以选择传入一个函数名，一个 `[$class, $method]` 或者 `Foo::bar` 这样的类型。

某些情况，我们需要直接使用 `$message` 参数，那么怎么在 `push` 的闭包外调用呢？

```php
    $message = $app->server->getMessage();
```
>  注意：`$message` 的类型取决于你的配置中 `response_type`

## 注册多个消息处理器

有时候你可能需要对消息记日志，或者一系列的自定义操作，你可以注册多个 handler：

```php
$app->server->push(MessageLogHandler::class);
$app->server->push(MessageReplyHandler::class);
$app->server->push(OtherHandler::class);
$app->server->push(...);
```

1. 最后一个非空返回值将作为最终应答给用户的消息内容，如果中间某一个 handler 返回值 false, 则将终止整个调用链，不会调用后续的 handlers。
2. 传入的自定义 Handler 类需要实现 `\EasyWeChat\Kernel\Contracts\EventHandlerInterface`。

## 注册指定消息类型的消息处理器

我们想对特定类型的消息应用不同的处理器，可以在第二个参数传入类型筛选：

> 注意，第二个参数必须是 `\EasyWeChat\Kernel\Messages\Message` 类的常量。

```php
use EasyWeChat\Kernel\Messages\Message;

$app->server->push(ImageMessageHandler::class, Message::IMAGE); // 图片消息
$app->server->push(TextMessageHandler::class, Message::TEXT); // 文本消息

// 同时处理多种类型的处理器
$app->server->push(MediaMessageHandler::class, Message::VOICE|Message::VIDEO|Message::SHORT_VIDEO); // 当消息为 三种中任意一种都可触发
```

## 请求消息的属性

当你接收到用户发来的消息时，可能会提取消息中的相关属性，参考：

请求消息基本属性(以下所有消息都有的基本属性)：

>>  - `ToUserName`    接收方帐号（该公众号 ID）
>>  - `FromUserName`  发送方帐号（OpenID, 代表用户的唯一标识）
>>  - `CreateTime`    消息创建时间（时间戳）
>>  - `MsgId`        消息 ID（64位整型）

### 文本：

>  - `MsgType`  text
>  - `Content`  文本消息内容

### 图片：

>  - `MsgType`  image
>  - `MediaId`  图片消息媒体id，可以调用多媒体文件下载接口拉取数据。
>  - `PicUrl`   图片链接

### 语音：

>  - `MsgType`        voice
>  - `MediaId`        语音消息媒体id，可以调用多媒体文件下载接口拉取数据。
>  - `Format`         语音格式，如 amr，speex 等
>  - `Recognition`  * 开通语音识别后才有

  > 识别后，用户每次发送语音给公众号时，微信会在推送的语音消息XML数据包中，增加一个 `Recongnition` 字段

### 视频：

>  - `MsgType`       video
>  - `MediaId`       视频消息媒体id，可以调用多媒体文件下载接口拉取数据。
>  - `ThumbMediaId`  视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据。

### 小视频：

>  - `MsgType`     shortvideo
>  - `MediaId`     视频消息媒体id，可以调用多媒体文件下载接口拉取数据。
>  - `ThumbMediaId`    视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据。

### 事件：

>  - `MsgType`     event
>  - `Event`       事件类型 （如：subscribe(订阅)、unsubscribe(取消订阅) ...， CLICK 等）

#### 扫描带参数二维码事件
>  - `EventKey`    事件KEY值，比如：qrscene_123123，qrscene_为前缀，后面为二维码的参数值
>  - `Ticket`      二维码的 ticket，可用来换取二维码图片

#### 上报地理位置事件
>  - `Latitude`    23.137466   地理位置纬度
>  - `Longitude`   113.352425  地理位置经度
>  - `Precision`   119.385040  地理位置精度

#### 自定义菜单事件
>  - `EventKey`    事件KEY值，与自定义菜单接口中KEY值对应，如：CUSTOM_KEY_001, www.qq.com

### 地理位置：

>  - `MsgType`     location
>  - `Location_X`  地理位置纬度
>  - `Location_Y`  地理位置经度
>  - `Scale`       地图缩放大小
>  - `Label`       地理位置信息

### 链接：

>  - `MsgType`      link
>  - `Title`        消息标题
>  - `Description`  消息描述
>  - `Url`          消息链接

### 文件：

>  - `MsgType`      file
>  - `Title`        文件名
>  - `Description`  文件描述，可能为null
>  - `FileKey`      文件KEY
>  - `FileMd5`      文件MD5值
>  - `FileTotalLen` 文件大小，单位字节

## 回复消息

回复的消息可以为 `null`，此时 SDK 会返回给微信一个 "SUCCESS"，你也可以回复一个普通字符串，比如：`欢迎关注 overtrue.`，此时 SDK 会对它进行一个封装，产生一个 [`EasyWeChat\Kernel\Messages\Text`](https://github.com/EasyWeChat/message/blob/master/src/Kernel/Messages/Text.php) 类型的消息并在最后的 `$app->server->serve();` 时生成对应的消息 XML 格式。

如果你想返回一个自己手动拼的原生 XML 格式消息，请返回一个 [`EasyWeChat\Kernel\Messages\Raw`](https://github.com/EasyWeChat/message/blob/master/src/Kernel/Messages/Raw.php) 实例即可。

## 消息转发给客服系统

参见：[多客服消息转发](message-transfer)

关于消息的使用，请参考 [`消息`](messages) 章节。
