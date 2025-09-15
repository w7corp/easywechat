# 临时素材

上传的临时多媒体文件有格式和大小限制，如下：

> - 图片（image）: 2M，支持 `JPG` 格式
> - 语音（voice）：2M，播放长度不超过 `60s`，支持 `AMR\MP3` 格式
> - 视频（video）：10MB，支持 `MP4` 格式
> - 缩略图（thumb）：64KB，支持 `JPG` 格式

## 上传多媒体文件

> 注意：微信图片上传服务有敏感检测系统，图片内容如果含有敏感内容，如色情，商品推广，虚假信息等，上传可能失败。

```php
// 上传图片
$response = $app->getClient()->post('/cgi-bin/media/upload', [
    'query' => ['type' => 'image'],
    'body' => [
        'media' => fopen($imagePath, 'r')
    ]
]);

// 上传声音
$response = $app->getClient()->post('/cgi-bin/media/upload', [
    'query' => ['type' => 'voice'],
    'body' => [
        'media' => fopen($voicePath, 'r')
    ]
]);

// 上传视频
$response = $app->getClient()->post('/cgi-bin/media/upload', [
    'query' => ['type' => 'video'],
    'body' => [
        'media' => fopen($videoPath, 'r')
    ]
]);

// 上传缩略图
$response = $app->getClient()->post('/cgi-bin/media/upload', [
    'query' => ['type' => 'thumb'],
    'body' => [
        'media' => fopen($thumbPath, 'r')
    ]
]);

$result = $response->toArray();
// 返回结果包含 media_id
// {
//   "type": "image",
//   "media_id": "MEDIA_ID",
//   "created_at": 123456789
// }
```

## 上传群发视频

上传视频获取 `media_id` 用以创建群发消息用。

```php
$response = $app->getClient()->postJson('/cgi-bin/media/uploadvideo', [
    'media_id' => $mediaId,
    'title' => $title,
    'description' => $description
]);

$result = $response->toArray();
// {
//   "media_id": "rF4UdIMfYK3efUfyoddYRMU50zMiRmmt_l0kszupYh_SzrcW5Gaheq05p_lHuOTQ",
//   "title": "TITLE",
//   "description": "Description"
// }
```

## 获取临时素材内容

```php
$response = $app->getClient()->get('/cgi-bin/media/get', [
    'query' => ['media_id' => $mediaId]
]);

// 注意：获取临时素材会返回文件内容，需要特殊处理
// 可以将响应内容保存为文件
$content = $response->getContent();
file_put_contents('保存路径/文件名', $content);
```

## 获取 JSSDK 上传的高清语音

```php
$response = $app->getClient()->get('/cgi-bin/media/get/jssdk', [
    'query' => ['media_id' => $mediaId]
]);

$content = $response->getContent();
file_put_contents('保存路径/voice.speex', $content);
```
