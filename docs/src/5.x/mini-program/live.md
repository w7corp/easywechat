# 订阅消息

> 微信文档：https://developers.weixin.qq.com/miniprogram/dev/framework/liveplayer/live-player-plugin.html

> tips:微信规定以下两个接口调用限制共享 **500次/天** 建议开发者自己做缓存，合理分配调用频次。

## 获取直播房间列表

```php
$app->live->getRooms();
```

## 获取回放源视频

```php
$roomId = 1;    //直播房间id

$app->live->getPlaybacks($roomId);
```