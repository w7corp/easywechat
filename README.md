# Wechat
微信 SDK

[![Build Status](https://travis-ci.org/overtrue/wechat.svg?branch=master)](https://travis-ci.org/overtrue/wechat)
[![Latest Stable Version](https://poser.pugx.org/overtrue/wechat/v/stable.svg)](https://packagist.org/packages/overtrue/wechat)
[![Latest Unstable Version](https://poser.pugx.org/overtrue/wechat/v/unstable.svg)](https://packagist.org/packages/overtrue/wechat)
[![Build Status](https://scrutinizer-ci.com/g/overtrue/wechat/badges/build.png?b=master)](https://scrutinizer-ci.com/g/overtrue/wechat/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/overtrue/wechat/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/overtrue/wechat/?branch=master)

[![Total Downloads](https://poser.pugx.org/overtrue/wechat/downloads.svg)](https://packagist.org/packages/overtrue/wechat)
[![Monthly Downloads](https://poser.pugx.org/overtrue/wechat/d/monthly.png)](https://packagist.org/packages/overtrue/wechat)
[![Daily Downloads](https://poser.pugx.org/overtrue/wechat/d/daily.png)](https://packagist.org/packages/overtrue/wechat)


> 努力coding中，目前只完成了文档中列出的功能，敬请期待

网上充斥着各种微信SDK，但是找了一圈，发现没有一个想用，因为没有满足本项目存在后的各种优点：

 - 命名不那么乱七八糟;
 - 隐藏开发者不需要关注的细节;

    > 例如上传媒体文件吧，因为开发者目的是：`发送一条图片消息给用户`，而不是：`上传一张图片得到media_id,然后发送media_id给用户`。

 - 方法使用更优雅，不再那么恶心的使用恶心的命名譬如：`getXML4Image...`；
 - 统一的错误处理，让你更方便的掌控异常；
 - 自定义缓存方式
 - 符合PSR标准，你可以各种方便的与你的框架集成。

## Install

```shell
composer require overtrue/wechat
```

## Usage

基本使用

```php
<?php

use Overtrue\Wechat\Wechat;

$options = [
    'app_id'         => 'Your appid',
    'secret'         => 'Your secret'
    'token'          => 'Your token',
    'encodingAESKey' => 'Your encodingAESKey' // optional
];

// 初始化Wechat实例
$wechat = new Wechat($options);

// 接收消息
$server = $wechat->on('message', function($message){
    error_log("收到来自'{$message['FromUserName']}'的消息：" . $message['Content']);
});

$result = $wechat->serve();  //获取上面各种事件触发运行结果

// 返回值$result为字符串，您可以直接用于echo 或者返回给框架
echo $result;
```

---


### 基础

+ 初始化
    
```php
<?php

use Overtrue\Wechat\Wechat;

$options = [
    'app_id'         => 'Your appid',
    'secret'         => 'Your secret'
    'token'          => 'Your token',
    'encodingAESKey' => 'Your encodingAESKey' // 可选
];

// 初始化Wechat实例
$wechat = new Wechat($options);
```

+ 接收用户发来的消息(回复)

  ```php
  $wechat->on('message', callable $callback);
  // or 
  $wechat->on('message', string $messageType, callable $callback);
  ```
    
  参数说明 
  
  - `$messageType` string, 指定要处理的消息类型，ex：`image`
  - `$callback` callable, 回调函数，closure匿名函数，或者一切可调用的方法或者函数

  example:
  
  ```php
  // 监听所有类型
  $wechat->on('message', function($message) use ($wechat) {
      // 所有类型的消息都会触发此函数
      error_log("收到来自{$message['FromUserName']}， 消息类型为:{$message['MsgType']}");        
  
      // 回复一条消息
      return $wechat->message('text')->content('您好！');
  });
  
  // 监听指定类型
  $wechat->on('message', 'image', function($message) use ($wechat) {
      //只有收到图片(image)类型触发此函数
      error_log("收到来自{$message['FromUserName']}的图片消息");        
  
      return $wechat->message('text')->content('我们已经收到您发送的图片！');
  });

  $result = $wechat->serve();  //获取上面各种事件触发运行结果

  // 返回值$result为字符串，您可以直接用于echo 或者返回给框架
  echo $result;
  ```

+ 订阅微信事件

  ```php
  $wechat->on('event',  callable $callback);
  // or 
  $wechat->on('event',  string $eventType, callable $callback);
  ```

  参数说明
  
  - `$eventType` string, 指定要处理的消息类型，ex：`image`
  - `$callback` callable, 回调函数，closure匿名函数，或者一切可调用的方法或者函数
  
  example:
  
  ```php
  // 监听所有事件
  $wechat->on('event', function($event) use ($wechat) {
  
      error_log('收到取消关注事件，取消关注者openid: ' . $event['FromUserName']);      
  });
  
  // 只监听指定类型事件
  $wechat->on('event', 'subscribe', function($event) use ($wechat) {
  
      error_log('收到关注事件，关注者openid: ' . $event['FromUserName']);      
  
      return $wechat->message('text')->content('感谢您关注');
  });

  $result = $wechat->serve();  //获取上面各种事件触发运行结果

  // 返回值$result为字符串，您可以直接用于echo 或者返回给框架
  echo $result;
  ```

## 用户

  ```php
  $user = $wechat->user;
  ```

+ `$user->get($openId);` 获取用户信息
+ `$user->all($nextOpenId = null);` 获取用户列表, $nextOpenId 可选
+ `$user->remark($openId, $remark);` 修改用户备注

## 用户组 

  ```php
  $group = $wechat->group;
  ```

+ `$group->all();` 获取所有分组
+ `$group->update($groupId, $name);` 修改分组信息
+ `$group->moveUser($openId, $groupId);` 移动单个用户到指定分组
+ `$group->moveUsers(array $openIds, $groupId);` 批量移动用户到指定分组

## 客服

  ```php
  $staff = $wechat->staff;
  ```

+ `$staff->all();` 获取所有客服账号
+ `$staff->allOnline();` 获取所有在线的客服账号
+ `$staff->create($mail, $nickname, $password);` 添加客服帐号
+ `$staff->update($mail, $nickname, $password);` 修改客服帐号
+ `$staff->delete($mail, $nickname, $password);` 删除客服帐号
+ `$staff->avatar($mail, $avatarPath);` 设置客服帐号的头像
+ `$staff->send($message)->to($openId);` 主动发送消息给用户
+ 群发消息

  ```php
  // 所有人
  $staff->send($message)->toAll(); 
  // 指定组
  $staff->send($message)->toGroup($groupId); 
  // 多个人
  $staff->send($message)->toMany(array($openId, $openId, ...)); 
  ```

+ `$staff->transfer($message); ` 消息转发给全部客服
+ `$staff->transfer($message, $stuffMail); ` 消息转发给单个客服
    

## 网页授权

  ```php
  $wechat->auth;
  ```

+ 生成授权链接

  ```php
  // 生成并返回
  $wechat->auth->url($to, $state, $scope);
  // 直接跳转
  $wechat->auth->redirect($to, $state, $scope);   直接跳转
  ```

+ 判断是否已经授权

  ```php
  $wechat->auth->authorized();
  ```

+ 获取授权用户

  ```php
  $wechat->auth->user();
  ```

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
      // 得到错误码：参考：http://mp.weixin.qq.com/wiki/17/fa4e1434e57290788bde25603fa2fcbd.html
      // $error->getMessage(); 错误消息
  });
  ```

## 自定义缓存写入/读取

  ```php
  // 写入
  $wechat->cache->setter(function($key, $value, $lifetime){
      // cache the value.
      return your_custom_set_cache($key, $value, $lifetime);
  });
  
  // 读取
  $wechat->cache->getter(function($key){
      // return the cached value.
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
| 音乐     | `music`    | `title` 标题 <br>`description` 描述 <br>`url` 音乐URL <br>`hq_url` 高清URL <br>`thumb_media_id` 封面资源id | `url($musicUrl)` <br>`hqUrl($music)` <br>`thumb($path)` |
| 视频     | `video`    | `title` 标题 <br>`description` 描述 <br>`media_id` 媒体资源id <br>`thumb_media_id` 封面资源id        | `media($path)` <br>`thumb($path)`                 |
| 位置     | `location` | `lat` 地理位置纬度 <br>`lon` 地理位置经度 <br>`scale` 地图缩放大小 <br>`label` 地理位置信息          |                                           |
| 链接     | `link`     | `title` 标题 <br>`description` 描述<br>url  链接URL                                          |                                           |

### 创建消息

```php
<?php

use Overtrue\Wechat\Wechat;
use Overtrue\Wechat\Message;

$options = array(...);

$wechat = new Wechat($options);

$wechat->on('event', 'subscribe', function($event){
  //创建一条文本消息
  $message = Message::make('text');
  $message->content = '您好！欢迎关注overtrue';
  $message->to = $event['FromUserName'];

  // 回复给用户
  return $message;
});
```

当然，消息是支持链式操作的，比如上面的例子可以写成：

```php
$message = Message::make('text')->content('您好！欢迎关注overtrue')->to($openId);
```
再或者:

```php
$message = $wecaht->message('text')->content('您好！欢迎关注overtrue')->to($openId);
```

这里有一点需要注意，当属性带下划线的时候，方法名是支持两种的：`media_id()` 或者 `mediaId()` 都一样。

### 上传媒体文件

媒体文件你不用上传，也就是说media_id是我来维护，你直接传本地文件就好了。

```php
$message = Message::make('image')->media('D:/test/demo.jpg');
```

方法`media($file)`会上传文件然后赋值到`media_id`属性。如果想要获取上传后的media_id: 

```php
$mediaId = $message->media_id;
```

#### 这里有两个方法用于设置媒体文件：

- `media($file)` 对应设置 `media_id`
- `thumb($file)` 对应设置 `thumb_media_id`

## License

MIT
