# 素材管理

在微信里的图片，音乐，视频等等都需要先上传到微信服务器作为素材才可以在消息中使用。

### 上传图片

> 注意：微信图片上传服务有敏感检测系统，图片内容如果含有敏感内容，如色情，商品推广，虚假信息等，上传可能失败。

```php
$result = $app->material->uploadImage("/path/to/your/image.jpg");
// {
//    "media_id":MEDIA_ID,
//    "url":URL
// }
```

> `url` 只有上传图片素材有返回值。

### 上传语音

语音 **大小不超过 5M**，**长度不超过 60 秒**，支持 `mp3/wma/wav/amr` 格式。

```php
$result = $app->material->uploadVoice("/path/to/your/voice.mp3");
// {
//    "media_id":MEDIA_ID,
// }
```

### 上传视频

```php
$result = $app->material->uploadVideo("/path/to/your/video.mp4", "视频标题", "视频描述");
// {
//    "media_id":MEDIA_ID,
// }
```

### 上传缩略图

用于视频封面或者音乐封面。

```php
$result = $app->material->uploadThumb("/path/to/your/thumb.jpg");
// {
//    "media_id":MEDIA_ID,
// }
```

### 上传图文消息

```php
use EasyWeChat\Kernel\Messages\Article;

// 上传单篇图文
$article = new Article([
    'title' => 'xxx',
    'thumb_media_id' => $mediaId,
    //...
  ]);
$app->material->uploadArticle($article);

// 或者多篇图文
$app->material->uploadArticle([$article, $article2, ...]);
```

### 修改图文消息

有三个参数：

> - `$mediaId` 要更新的文章的 `mediaId`
> - `$article` 文章内容，`Article` 实例或者 全字段数组
> - `$index` 要更新的文章在图文消息中的位置（多图文消息时，此字段才有意义，单图片忽略此参数），第一篇为 0；

```php
$result = $app->material->updateArticle($mediaId, new Article(...));

// or

$result = $app->material->updateArticle($mediaId, [
   'title' => 'EasyWeChat 4.0 发布了！',
    'thumb_media_id' => 'qQFxUQGO21Li4YrSn3MhnrqtRp9Zi3cbM9uBsepvDmE', // 封面图片 mediaId
    'author' => 'overtrue', // 作者
    'show_cover' => 1, // 是否在文章内容显示封面图片
    'digest' => '这里是文章摘要',
    'content' => '这里是文章内容，你可以放很长的内容',
    'source_url' => 'https://www.easywechat.com',
  ]);

// 指定更新多图文中的第 2 篇
$result = $app->material->updateArticle($mediaId, new Article(...), 1); // 第 2 篇
```

### 上传图文消息图片

返回值中 url 就是上传图片的 URL，可用于后续群发中，放置到图文消息中。

```php
$result = $app->material->uploadArticleImage($path);
//{
//    "url":  "http://mmbiz.qpic.cn/mmbiz/gLO17UPS6FS2xsypf378iaNhWacZ1G1UplZYWEYfwvuU6Ont96b1roYsCNFwaRrSaKTPCUdBK9DgEHicsKwWCBRQ/0"
//}
```

### 获取永久素材

```php
$resource = $app->material->get($mediaId);
```

如果请求的素材为图文消息，则响应如下：

```json
{
 "news_item": [
       {
       "title":TITLE,
       "thumb_media_id"::THUMB_MEDIA_ID,
       "show_cover_pic":SHOW_COVER_PIC(0/1),
       "author":AUTHOR,
       "digest":DIGEST,
       "content":CONTENT,
       "url":URL,
       "content_source_url":CONTENT_SOURCE_URL
       },
       //多图文消息有多篇文章
    ]
  }
```

如果返回的是视频消息素材，则内容如下：

```json
{
  "title": TITLE,
  "description": DESCRIPTION,
  "down_url": DOWN_URL
}
```

其他类型的素材消息，则响应为 `EasyWeChat\Kernel\Http\StreamResponse` 实例，开发者可以自行保存为文件。例如

```php
$stream = $app->material->get($mediaId);

if ($stream instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
    // 以内容 md5 为文件名
    $stream->save('保存目录');

    // 自定义文件名，不需要带后缀
    $stream->saveAs('保存目录', '文件名');
}
```

### 获取永久素材列表

> - `$type` 素材的类型，图片（`image`）、视频（`video`）、语音 （`voice`）、图文（`news`）
> - `$offset` 从全部素材的该偏移位置开始返回，可选，默认 `0`，0 表示从第一个素材 返回
> - `$count` 返回素材的数量，可选，默认 `20`, 取值在 1 到 20 之间

```php
$app->material->list($type, $offset, $count);
```

示例：

```php
$list = $app->material->list('image', 0, 10);
```

图片、语音、视频 等类型的返回如下

```json
{
  "total_count": TOTAL_COUNT,
  "item_count": ITEM_COUNT,
  "item": [
    {
      "media_id": MEDIA_ID,
      "name": NAME,
      "update_time": UPDATE_TIME,
      "url": URL
    }
    //可能会有多个素材
  ]
}
```

永久图文消息素材列表的响应如下：

```json
{
  "total_count": TOTAL_COUNT,
  "item_count": ITEM_COUNT,
  "item": [
    {
      "media_id": MEDIA_ID,
      "content": {
        "news_item": [
          {
            "title": TITLE,
            "thumb_media_id": THUMB_MEDIA_ID,
            "show_cover_pic": SHOW_COVER_PIC(0 / 1),
            "author": AUTHOR,
            "digest": DIGEST,
            "content": CONTENT,
            "url": URL,
            "content_source_url": CONTETN_SOURCE_URL
          }
          //多图文消息会在此处有多篇文章
        ]
      },
      "update_time": UPDATE_TIME
    }
    //可能有多个图文消息item结构
  ]
}
```

### 获取素材计数

```php
$stats = $app->material->stats();

// {
//   "voice_count":COUNT,
//   "video_count":COUNT,
//   "image_count":COUNT,
//   "news_count":COUNT
// }
```

### 删除永久素材；

```php
$app->material->delete($mediaId);
```

### 文章预览

文章预览请参阅 “消息群发” 章节。
