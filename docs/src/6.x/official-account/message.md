# 消息

公众号消息分为 [**服务端被动回复消息**](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Passive_user_reply_message.html) 和 [**客服消息**](https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Service_Center_messages.html) 两个场景。

需要注意的是两个场景的消息虽然类似，但是结构却有些差异，比如服务端使用 XML 结构，而客服消息使用 JSON 结构，且同样类似的消息类型，结构和名称都有些许差异，在使用时请勿混淆。

## 服务端消息结构

当你接收到用户发来的消息时，可能会提取消息中的相关属性，参考：

请求消息基本属性(以下所有消息都有的基本属性)：

```
  - `ToUserName`    接收方帐号（该公众号 ID）
  - `FromUserName`  发送方帐号（OpenID, 代表用户的唯一标识）
  - `CreateTime`    消息创建时间（时间戳）
  - `MsgId`        消息 ID（64位整型）
```

### 文本

```
  - `MsgType`  text
  - `Content`  文本消息内容
```

### 图片

```
  - `MsgType`  image
  - `MediaId`  图片消息媒体id，可以调用多媒体文件下载接口拉取数据。
  - `PicUrl`   图片链接
```

### 语音

```
  - `MsgType`        voice
  - `MediaId`        语音消息媒体id，可以调用多媒体文件下载接口拉取数据。
  - `Format`         语音格式，如 amr，speex 等
  - `Recognition`  * 开通语音识别后才有
```

> 请注意，开通语音识别后，用户每次发送语音给公众号时，微信会在推送的语音消息 XML 数据包中，增加一个 `Recongnition` 字段

### 视频

```
  - `MsgType`       video
  - `MediaId`       视频消息媒体id，可以调用多媒体文件下载接口拉取数据。
  - `ThumbMediaId`  视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据。
```

### 小视频

```
  - `MsgType`     shortvideo
  - `MediaId`     视频消息媒体id，可以调用多媒体文件下载接口拉取数据。
  - `ThumbMediaId`    视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据。
```

### 事件消息

```
  - `MsgType`     event
  - `Event`       事件类型 （如：subscribe(订阅)、unsubscribe(取消订阅) ...， CLICK 等）
```

#### 扫描带参数二维码事件

```
  - `EventKey`    事件KEY值，比如：qrscene_123123，qrscene_为前缀，后面为二维码的参数值
  - `Ticket`      二维码的 ticket，可用来换取二维码图片
```

#### 上报地理位置事件

```
  - `Latitude`    23.137466   地理位置纬度
  - `Longitude`   113.352425  地理位置经度
  - `Precision`   119.385040  地理位置精度
```

#### 自定义菜单事件

```
  - `EventKey`    事件KEY值，与自定义菜单接口中KEY值对应，如：CUSTOM_KEY_001, www.qq.com
```

### 地理位置

```
  - `MsgType`     location
  - `Location_X`  地理位置纬度
  - `Location_Y`  地理位置经度
  - `Scale`       地图缩放大小
  - `Label`       地理位置信息
```

### 链接

```
  - `MsgType`      link
  - `Title`        消息标题
  - `Description`  消息描述
  - `Url`          消息链接
```

### 文件

```
  - `MsgType`      file
  - `Title`        文件名
  - `Description`  文件描述，可能为null
  - `FileKey`      文件KEY
  - `FileMd5`      文件MD5值
  - `FileTotalLen` 文件大小，单位字节
```

## 客服消息结构

### 发送文本消息

```json
{
  "touser": "OPENID",
  "msgtype": "text",
  "text": {
    "content": "Hello World"
  }
}
```

### 图片消息

```json
{
  "touser": "OPENID",
  "msgtype": "image",
  "image": {
    "media_id": "MEDIA_ID"
  }
}
```

### 语音消息

```json
{
  "touser": "OPENID",
  "msgtype": "voice",
  "voice": {
    "media_id": "MEDIA_ID"
  }
}
```

### 视频消息

```json
{
  "touser": "OPENID",
  "msgtype": "video",
  "video": {
    "media_id": "MEDIA_ID",
    "thumb_media_id": "MEDIA_ID",
    "title": "TITLE",
    "description": "DESCRIPTION"
  }
}
```

### 音乐消息

```json
{
  "touser": "OPENID",
  "msgtype": "music",
  "music": {
    "title": "MUSIC_TITLE",
    "description": "MUSIC_DESCRIPTION",
    "musicurl": "MUSIC_URL",
    "hqmusicurl": "HQ_MUSIC_URL",
    "thumb_media_id": "THUMB_MEDIA_ID"
  }
}
```

### 图文消息（点击跳转到外链）

```json
{
  "touser": "OPENID",
  "msgtype": "news",
  "news": {
    "articles": [
      {
        "title": "Happy Day",
        "description": "Is Really A Happy Day",
        "url": "URL",
        "picurl": "PIC_URL"
      }
    ]
  }
}
```

### 图文消息（点击跳转到图文消息页面）

```json
{
  "touser": "OPENID",
  "msgtype": "mpnews",
  "mpnews": {
    "media_id": "MEDIA_ID"
  }
}
```

### 菜单消息

```json
{
  "touser": "OPENID",
  "msgtype": "msgmenu",
  "msgmenu": {
    "head_content": "您对本次服务是否满意呢? "
    "list": [
      {
        "id": "101",
        "content": "满意"
      },
      {
        "id": "102",
        "content": "不满意"
      }
    ],
    "tail_content": "欢迎再次光临"
  }
}
```

### 卡券消息

```json
{
  "touser": "OPENID",
  "msgtype": "wxcard",
  "wxcard": {
    "card_id": "123dsdajkasd231jhksad"
  }
}
```

> 请以官方文档为准。
