# Wechat
微信 SDK

[![Build Status](https://travis-ci.org/overtrue/wechat.svg?branch=master)](https://travis-ci.org/overtrue/wechat)
[![Latest Stable Version](https://poser.pugx.org/overtrue/wechat/v/stable.svg)](https://packagist.org/packages/overtrue/wechat)
[![Latest Unstable Version](https://poser.pugx.org/overtrue/wechat/v/unstable.svg)](https://packagist.org/packages/overtrue/wechat)
[![Build Status](https://scrutinizer-ci.com/g/overtrue/wechat/badges/build.png?b=master)](https://scrutinizer-ci.com/g/overtrue/wechat/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/overtrue/wechat/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/overtrue/wechat/?branch=master)
[![Total Downloads](https://poser.pugx.org/overtrue/wechat/downloads.svg)](https://packagist.org/packages/overtrue/wechat)

网上充斥着各种微信SDK，但是找了一圈，发现没有一个想用，因为没有满足本项目存在后的各种优点：

 - 命名不那么乱七八糟;
 - 隐藏开发者不需要关注的细节;
 - 方法使用更优雅，不再那么恶心的使用恶心的命名譬如：`getXML4Image...`；
 - 统一的错误处理，让你更方便的掌控异常；
 - 自定义缓存方式;
 - 符合PSR-4标准，你可以各种方便的与你的框架集成;
 - 高度抽象的消息类，免去各种拼json与xml的痛苦。

## Install

requirement:

  - PHP >= 5.3.0

```shell
composer require overtrue/wechat
```

## Usage

基本使用

```php
<?php

use Overtrue\Wechat\Wechat;

$options = [
    'appId'          => 'Your app id',
    'secret'         => 'Your secret'
    'token'          => 'Your token',
    'encodingAESKey' => 'Your encoding AES Key' // optional
];

$wechat = Wechat::make($options);

$server = $wechat->on('message', function($message){
    error_log("收到来自'{$message['FromUserName']}'的消息：" . $message['Content']);
});

$result = $wechat->serve();

// 您可以直接echo 或者返回给框架
echo $result;
```

---

## 菜单

  ```php
  $wechat->menu;
  ```

+ `$menu->get();` 读取菜单
+ `$menu->set($menus);` 设置菜单
+ `$menu->delete();` 删除菜单

## 签名

+ 计算签名
  
  ```php
  $wechat->signature($params);
  ```

## Ticket
  
  ```php
  $wechat->ticket->js();
  $wechat->ticket->card();
  ```

---

## 处理错误

  ```php
  $wechat->error(function($error){
      // $error为Exception对象
      // $error->getCode(); 
      // 错误码：参考：http://mp.weixin.qq.com/wiki/17/fa4e1434e57290788bde25603fa2fcbd.html
      // $error->getMessage(); 错误消息
  });
  ```

## 自定义缓存写入/读取

  ```php
  // 写入
  $wechat->cache->setter(function($key, $value, $lifetime){
      return your_custom_set_cache($key, $value, $lifetime);
  });
  
  // 读取
  $wechat->cache->getter(function($key){
      return your_custom_get_cache($key);
  });
  ```

## 消息

我把微信的API里的所有“消息”都按类型抽象出来了，也就是说，你不用区分它是回复消息还是主动推送消息，免去了你去手动拼装微信那帮SB那么恶心的XML以及乱七八糟命名不统一的JSON了，我帮忙你承受这份苦。

### 消息的类型及属性

| 消息类型 | 类型名称 | 属性                                                                             | 除属性自身外提供的方法                                      |
|----------|----------|----------------------------------------------------------------------------------|-------------------------------------------|
| 文本     | `text`     | `content` 内容                                                                     |                                           |
| 图片     | `image`    | `media_id` 媒体资源id                                                              | `media($path)`                              |
| 声音     | `voice`    | `media_id` 媒体资源id                                                              | `media($path)`                              |
| 音乐     | `music`    | `title` 标题 <br>`description` 描述 <br>`url` 音乐URL <br>`hq_url` 高清URL <br>`thumb_media_id` 封面资源id | `thumb($path)` |
| 视频     | `video`    | `title` 标题 <br>`description` 描述 <br>`media_id` 媒体资源id <br>`thumb_media_id` 封面资源id        | `media($path)` <br>`thumb($path)`                 |
| 位置     | `location` | `lat` 地理位置纬度 <br>`lon` 地理位置经度 <br>`scale` 地图缩放大小 <br>`label` 地理位置信息          |                                           |
| 链接     | `link`     | `title` 标题 <br>`description` 描述<br>url  链接URL                                          |                                           |

### 创建消息

**请注意：消息类的命名空间为 `Overtrue\Wechat\Services\Message`**

```php
<?php

use Overtrue\Wechat\Wechat;
use Overtrue\Wechat\Services\Message;

$options = array(...);

$wechat = Wechat::make($options);

$wechat->on('event', 'subscribe', function($event){
  return Message::make('text')->content('您好！欢迎关注overtrue');
});
```

这里有一点需要注意，当属性带下划线的时候，方法名是支持两种的：`media_id()` 或者 `mediaId()` 都一样。

### 上传媒体文件


```php
$message = Message::make('image')->media('D:/test/demo.jpg');
```

媒体文件你不用上传，也就是说media_id是我来维护，你直接传本地文件就好了。
方法`media($file)`会上传文件然后赋值到`media_id`属性。如果想要获取上传后的media_id: 

```php
$mediaId = $message->media_id;
```

#### 这里有两个方法用于设置媒体文件：

- `media($file)` 对应设置 `media_id`
- `thumb($file)` 对应设置 `thumb_media_id`

## TODO

- [x] 用户
- [x] 用户组
- [x] 客服
- [x] 监听事件与消息
- [x] 基本消息类型
- [x] 图文消息
- [ ] 群发消息
- [ ] 菜单 
- [ ] Auth
- [ ] Ticket
- [ ] 二维码
- [ ] 短链接

## License

MIT
