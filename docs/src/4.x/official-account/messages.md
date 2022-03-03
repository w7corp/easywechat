# 消息

我把微信的 API 里的所有“消息”都按类型抽象出来了，也就是说，你不用区分它是回复消息还是主动推送消息，免去了你去手动拼装微信的 XML 以及乱七八糟命名不统一的 JSON 了。

在阅读以下内容时请忽略是 **接收消息** 还是 **回复消息**，后面我会给你讲它们的区别。

## 消息类型

消息分为以下几种：`文本`、`图片`、`视频`、`声音`、`链接`、`坐标`、`图文`、`文章` 和一种特殊的 `原始消息`。

另外还有一种特殊的消息类型：**素材消息**，用于群发或者客服时发送已有素材用。

> 注意：回复消息与客服消息里的图文类型为：**图文**，群发与素材中的图文为**文章**

所有的消息类都在 `EasyWeChat\Kernel\Messages` 这个命名空间下， 下面我们来分开讲解：

### 文本消息

属性列表：

> - `content` 文本内容

```php
use EasyWeChat\Kernel\Messages\Text;

$text = new Text('您好！overtrue。');

// or
$text = new Text();
$text->content = '您好！overtrue。';

// or
$text = new Text();
$text->setAttribute('content', '您好！overtrue。');
```

### 图片消息

属性列表：

```
- media_id 媒体资源 ID
```

```php

use EasyWeChat\Kernel\Messages\Image;

$image = new Image($mediaId);
```

### 视频消息

属性列表：

> - `title` 标题
> - `description` 描述
> - `media_id` 媒体资源 ID
> - `thumb_media_id` 封面资源 ID

```php

use EasyWeChat\Kernel\Messages\Video;

$video = new Video($mediaId, [
        'title' => $title,
        'description' => '...',
    ]);
```

### 声音消息

属性列表：

> - `media_id` 媒体资源 ID

```php
use EasyWeChat\Kernel\Messages\Voice;

$voice = new Voice($mediaId);
```

### 链接消息

> 复链接消息

### 坐标消息

> 复坐标消息

### 图文消息

图文消息分为 `NewsItem` 与 `News`，`NewsItem` 为图文内容条目。

> ，被动回复消息与客服消息接口的图文消息类型中图文数目只能为一条](https://mp.weixin.qq.com/cgi-bin/announce?action=getannouncement&announce_id=115383153198yAvN&version=&lang=zh_CN&token=)

`NewsItem` 属性：

> - `title` 标题
> - `description` 描述
> - `image` 图片链接
> - `url` 链接 URL

```php
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;

$items = [
    new NewsItem([
        'title'       => $title,
        'description' => '...',
        'url'         => $url,
        'image'       => $image,
        // ...
    ]),
];
$news = new News($items);
```

### 文章

属性列表：

> - `title` 标题
> - `author` 作者
> - `content` 具体内容
> - `thumb_media_id` 图文消息的封面图片素材 id（必须是永久 mediaID）
> - `digest` 图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空
> - `source_url` 来源 URL
> - `show_cover` 是否显示封面，0 为 false，即不显示，1 为 true，即显示

```php
use EasyWeChat\Kernel\Messages\Article;

$article = new Article([
        'title'   => 'EasyWeChat',
        'author'  => 'overtrue',
        'content' => 'EasyWeChat 是一个开源的微信 SDK，它... ...',
        // ...
    ]);

// or
$article = new Article();
$article->title   = 'EasyWeChat';
$article->author  = 'overtrue';
$article->content = '微信 SDK ...';
// ...
```

### 素材消息

素材消息用于群发与客服消息时使用。

> 素材消息不支持被动回复，如需被动回复素材消息，首先组装后，再 News 方法返回。

属性就一个：`media_id`。

在构造时有两个参数：

> - `$type` 素材类型，目前只支持：`mpnews`、 `mpvideo`、`voice`、`image` 等。
> - `$mediaId` 素材 ID，从接口查询或者上传后得到。

```php
use EasyWeChat\Kernel\Messages\Media;

$media = new Media($mediaId, 'mpnews');
```

以上呢，是所有微信支持的基本消息类型。

> 需要注意的是，你不需要关心微信的消息字段叫啥，因为这里我们使用了更标准的命名，然后最终在中间做了转换，所以你不需要关注。

### 原始消息

原始消息是一种特殊的消息，它的场景是：**你不想使用其它消息类型，你想自己手动拼消息**。比如，回复消息时，你想自己拼 XML，那么你就直接用它就可以了：

```php
use EasyWeChat\Kernel\Messages\Raw;

$message = new Raw('<xml>
<ToUserName><![CDATA[toUser]]></ToUserName>
<FromUserName><![CDATA[fromUser]]></FromUserName>
<CreateTime>12345678</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
<Image>
<MediaId><![CDATA[media_id]]></MediaId>
</Image>
</xml>');
```

比如，你要用于客服消息(客服消息是 JSON 结构)：

```php
use EasyWeChat\Kernel\Messages\Raw;

$message = new Raw('{
    "touser":"OPENID",
    "msgtype":"text",
    "text":
    {
         "content":"Hello World"
    }
}');
```

总之，就是直接写微信接口要求的格式内容就好，此类型消息在 SDK 中不存在转换行为，所以请注意不要写错格式。

## 在 SDK 中使用消息

### 在服务端回复消息

在 [服务端](server) 一节中，我们讲了回复消息的写法：

```php
// ... 前面部分省略
$app->server->push(function ($message) {
    return "您好！欢迎关注我!";
});

$response = $server->serve();
```

上面 `return` 了一句普通的文本内容，这里只是为了方便大家，实际上最后会有一个隐式转换为 `Text` 类型的动作。

如果你要回复其它类型的消息，就需要返回一个具体的实例了，比如回复一个图片类型的消息：

```php
use EasyWeChat\Kernel\Messages\Image;
// ...
$app->server->push(function ($message) {
    return new Image('media-id');
});
// ...
```

#### 回复多图文消息

> ，被动回复消息与客服消息接口的图文消息类型中图文数目只能为一条](https://mp.weixin.qq.com/cgi-bin/announce?action=getannouncement&announce_id=115383153198yAvN&version=&lang=zh_CN&token=)

多图文消息其实就是单图文消息的一个数组而已了：

```php
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;

// ...
$app->server->push(function ($message) {
   $news = new NewsItem(...);
   return new News([$news]);
});
// ...
```

### 作为客服消息发送

在客服消息里的使用也一样，都是直接传入消息实例即可：

```php
use EasyWeChat\Kernel\Messages\Text;

$message = new Text('Hello world!');

$result = $app->customer_service->message($message)->to($openId)->send();
//...
```

#### 发送多图文消息

> ，被动回复消息与客服消息接口的图文消息类型中图文数目只能为一条](https://mp.weixin.qq.com/cgi-bin/announce?action=getannouncement&announce_id=115383153198yAvN&version=&lang=zh_CN&token=)

多图文消息其实就是单图文消息组成的一个 News 对象而已：

```php
$news1 = new NewsItem(...);
$news = new News([$news1]);

$app->customer_service->message($news)->to($openId)->send();
```

### 群发消息

请参考：[群发消息](broadcasting)

## 消息转发给客服系统

参见：[多客服消息转发](message-transfer)
