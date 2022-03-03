# 群发


微信的群发消息接口有各种乱七八糟的注意事项及限制，具体请阅读微信官方文档：http://mp.weixin.qq.com/wiki/15/5380a4e6f02f2ffdc7981a8ed7a40753.html

## 获取实例

```php
<?php
use EasyWeChat\Foundation\Application;
// ...
$app = new Application($options);

$broadcast = $app->broadcast;

```

## API

> 注意：

    下面提到的 `$messageType` 、`$message` 可以是：

    - `$messageType = Broadcast::MSG_TYPE_NEWS;` 图文消息类型，所对应的 `$message` 为 media_id
    - `$messageType = Broadcast::MSG_TYPE_TEXT;` 文本消息类型，所对应的 `$message` 为一个文本字符串
    - `$messageType = Broadcast::MSG_TYPE_VOICE;` 语音消息类型，所对应的 `$message` 为 media_id
    - `$messageType = Broadcast::MSG_TYPE_IMAGE;` 图片消息类型，所对应的 `$message` 为 media_id
    - `$messageType = Broadcast::MSG_TYPE_CARD;` 卡券消息类型，所对应的 `$message` 为 card_id
    - `$messageType = Broadcast::MSG_TYPE_VIDEO;` 视频消息为两种情况：
        - 视频消息类型，群发视频消息给**组或预览群发视频消息**给用户时所对应的 `$message` 为`media_id`
        - 群发视频消息**给指定用户**时所对应的 `$message` 为一个数组 `['MEDIA_ID', 'TITLE', 'DESCRIPTION']`


### 群发消息给所有粉丝

```php
$broadcast->send($messageType, $message);

// 别名方式
$broadcast->sendText("大家好！欢迎使用 EasyWeChat。");
$broadcast->sendNews($mediaId);
$broadcast->sendVoice($mediaId);
$broadcast->sendImage($mediaId);
//视频：
// - 群发给组用户，或者预览群发视频时 $message 为 media_id
// - 群发给指定用户时为数组：[$media_Id, $title, $description]
$broadcast->sendVideo($message);
$broadcast->sendCard($cardId);
```

### 群发消息给指定组

```php
$broadcast->send($messageType, $message, $groupId);

// 别名方式
$broadcast->sendText($text, $groupId);
$broadcast->sendNews($mediaId, $groupId);
$broadcast->sendVoice($mediaId, $groupId);
$broadcast->sendImage($mediaId, $groupId);
$broadcast->sendVideo($message, $groupId);
$broadcast->sendCard($cardId, $groupId);
```

### 群发消息给指定用户

至少两个用户的openid，必须是数组。

```php
$broadcast->send($messageType, $message, [$openId1, $openId2]);

// 别名方式
$broadcast->sendText($text, [$openId1, $openId2]);
$broadcast->sendNews($mediaId, [$openId1, $openId2]);
$broadcast->sendVoice($mediaId, [$openId1, $openId2]);
$broadcast->sendImage($mediaId, [$openId1, $openId2]);
$broadcast->sendVideo($message, [$openId1, $openId2]);
$broadcast->sendCard($cardId, [$openId1, $openId2]);
```

### 发送预览群发消息给指定的 `openId` 用户

```php
$broadcast->preview($messageType, $message, $openId);

// 别名方式
$broadcast->previewText($text, $openId);
$broadcast->previewNews($mediaId, $openId);
$broadcast->previewVoice($mediaId, $openId);
$broadcast->previewImage($mediaId, $openId);
$broadcast->previewVideo($message, $openId);
$broadcast->previewCard($cardId, $openId);
```

### 发送预览群发消息给指定的微信号用户

```php
$broadcast->previewByName($messageType, $message, $wxname);

// 别名方式
$broadcast->previewTextByName($text, $wxname);
$broadcast->previewNewsByName($mediaId, $wxname);
$broadcast->previewVoiceByName($mediaId, $wxname);
$broadcast->previewImageByName($mediaId, $wxname);
$broadcast->previewVideoByName($message, $wxname);
$broadcast->previewCardByName($cardId, $wxname);
```

### 删除群发消息

```php
$broadcast->delete($msgId);
```

### 查询群发消息发送状态

```php
$broadcast->status($msgId);
```

有关群发信息的更多细节请参考微信官方文档：http://mp.weixin.qq.com/wiki/
