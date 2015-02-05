# Wechat
微信 SDK

[![Build Status](https://scrutinizer-ci.com/g/overtrue/wechat/badges/build.png?b=master)](https://scrutinizer-ci.com/g/overtrue/wechat/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/overtrue/wechat/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/overtrue/wechat/?branch=master)


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
    'app_id'         => 'Your appid !!',
    'secret'         => 'Your secret !!'
    'token'          => 'Your token !!',
    'encodingAESKey' => 'Your encodingAESKey!!' // optional
];

// 初始化Wechat实例
$wechat = Wechat::make($options);

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
    'app_id'         => 'Your appid !!',
    'secret'         => 'Your secret !!'
    'token'          => 'Your token !!',
    'encodingAESKey' => 'Your encodingAESKey!!' // optional
];

// 初始化Wechat实例
$wechat = Wechat::make($options);
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
  $wechat->on('message', function($message){
      // 所有类型的消息都会触发此函数
      error_log("收到来自{$message['FromUserName']}， 消息类型为:{$message['MsgType']  }");        
  
      // 回复一条消息
      return $wechat->message('text')->content('您好！');
  });
  
  // 监听指定类型
  $wechat->on('message', 'image', function($message){
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
  $wechat->on('event', function($event){
  
      error_log('收到取消关注事件，取消关注者openid: ' . $event['FromUserName']);      
  });
  
  // 只监听指定类型事件
  $wechat->on('event', 'subscribe', function($event){
  
      error_log('收到关注事件，关注者openid: ' . $event['FromUserName']);      
  
      return $wechat->message('text')->content('感谢您关注');
  });

  $result = $wechat->serve();  //获取上面各种事件触发运行结果

  // 返回值$result为字符串，您可以直接用于echo 或者返回给框架
  echo $result;
  ```

## 客服

  ```php
  $wechat->staff;
  ```

+ 获取所有客服账号

  ```php
  $wechat->staff->all();`
  ```

+ 获取所有在线的客服账号

  ```php
  $wechat->staff->onlineAll();
  ```

+ 添加客服帐号

  ```php
  $wechat->staff->create($mail, $nickname, $password);
  ```

+ 修改客服帐号

  ```php
  $wechat->staff->update($mail, $nickname, $password);
  ```

+ 删除客服帐号

  ```php
  $wechat->staff->delete($mail, $nickname, $password);
  ```

+ 设置客服帐号的头像

  ```php
  $wechat->staff->avatar($mail, $avatarPath);
  ```

+ 主动发送消息给用户

  ```php  
  $wechat->staff->send($message)->to($openId);
  ```

+ 群发消息

  ```php
  // 所有人
  $wechat->staff->send($message)->toAll(); 
  // 指定组
  $wechat->staff->send($message)->toGroup($groupId); 
  // 多个人
  $wechat->staff->send($message)->toMany(array $groupIds); 
  ```
+ 消息转发给多个客服
 
  ```php
  $message->transfer(); 
  ```

+ 消息转发给单个客服
    
  ```php
  $message->transfer($stuffMail); 
  ```

## 用户

  ```php
  $wechat->user;
  ```

+ 获取用户信息

  ```php
  $user = $wechat->user->get($openId);
  ```

+ 获取用户列表

  ```php
  $users = $wechat->user->all();
  ```

+ 修改用户备注

  ```php
  $wechat->user->remark($openId, $remark);
  ```

## 用户组 

  ```php
  $wechat->group;
  ```

+ 获取所有分组

  ```php
  $wechat->group->all();
  ```

+ 修改分组信息

  ```php
  $wechat->group->update($id, $name);
  ```

+ 添加分组用户(批量移动用户)

  ```php
  // 移动单个用户
  $wechat->group->user($id, $openId);
  // 批量移动
  $wechat->group->users($id, $openIds);
  ``` 

## 网页授权

  ```php
  $wechat->auth;
  ```

+ 生成授权链接

  ```php
  // 生成并返回
  $wechat->auth->makeUrl($redirect, $state, $scope);
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

+ 读取菜单

  ```php
  $wechat->menu->get();
  ```

+ 设置菜单

  ```php  
  $wechat->menu->set($menus);
  ```

+ 删除菜单
  
  ```php
  $wechat->menu->delete();
  ```

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
      // 得到错误码：参考：http://mp.weixin.qq.com/wiki/17/    fa4e1434e57290788bde25603fa2fcbd.html
      // $error->getMessage(); 错误消息
  });
  ```

## 自定义缓存写入/读取

  ```php
  // writer
  $wechat->cacheWriter(function($key, $value){
      // cache the value.
      return true;
  });
  
  // reader
  $wechat->cacheReader(function($key){
      // return the cached value.
      return 缓存的数据;
  });
  ```

## License

MIT
