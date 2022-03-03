# 临时素材

上传的临时多媒体文件有格式和大小限制，如下：

> - 图片（image）: 2M，支持 `JPG` 格式
> - 语音（voice）：2M，播放长度不超过 `60s`，支持 `AMR\MP3` 格式
> - 视频（video）：10MB，支持 `MP4` 格式
> - 缩略图（thumb）：64KB，支持 `JPG` 格式

## 上传图片

> 注意：微信图片上传服务有敏感检测系统，图片内容如果含有敏感内容，如色情，商品推广，虚假信息等，上传可能失败。

```php
$app->media->uploadImage($path);
```

## 上传声音

```php
$app->media->uploadVoice($path);
```

## 上传视频

```php
$app->media->uploadVideo($path, $title, $description);
```

## 上传缩略图

用于视频封面或者音乐封面。

```php
$app->media->uploadThumb($path);
```

## 上传群发视频

上传视频获取 `media_id` 用以创建群发消息用。

```php
$app->media->uploadVideoForBroadcasting($path, $title, $description);

//{
//  "media_id": "rF4UdIMfYK3efUfyoddYRMU50zMiRmmt_l0kszupYh_SzrcW5Gaheq05p_lHuOTQ",
//  "title": "TITLE",
//  "description": "Description"
//}
```

## 创建群发消息

不要与上面 **上传群发视频** 搞混了，上面一个是上传视频得到 `media_id`，这个是使用该 `media_id` 加标题描述 **创建一条消息素材** 用来发送给用户。详情参见：[消息群发](../official-account/broadcasting.md)

```php
$app->media->createVideoForBroadcasting($mediaId, $title, $description);

//{
//  "type":"video",
//  "media_id":"IhdaAQXuvJtGzwwc0abfXnzeezfO0NgPK6AQYShD8RQYMTtfzbLdBIQkQziv2XJc",
//  "created_at":1398848981
//}
```

## 获取临时素材内容

比如图片、语音等二进制流内容，响应为 `EasyWeChat\Kernel\Http\StreamResponse` 实例。

```php
$stream = $app->media->get($mediaId);

if ($stream instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
  // 以内容 md5 为文件名存到本地
  $stream->save('保存目录');

  // 自定义文件名，不需要带后缀
  $stream->saveAs('保存目录', '文件名');
}
```

## 获取 JSSDK 上传的高清语音

```php
$stream = $app->media->getJssdkMedia($mediaId);
$stream->saveAs('保存目录', 'custom-name.speex');
```
