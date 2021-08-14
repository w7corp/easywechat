# 客服


> 2016.06.28 已经更新为新版多客服 API
> 请更新到 3.1 版本： composer require "overtrue/wechat:~3.1"

微信的客服才能发送消息或者群发消息，而且还有时效限制，真恶心的说。。。

## 客服管理

```php
<?php
use EasyWeChat\Foundation\Application;
// ...
$app = new Application($options);

$staff = $app->staff; // 客服管理
```

## API

### 获取所有客服账号列表

```php
$staff->lists();
```

### 获取所有在线的客服账号列表

```php
$staff->onlines();
```

### 添加客服帐号

```php
$staff->create('foo@test', '客服1');
```

### 修改客服帐号

```php
$staff->update('foo@test', '客服1');
```

### 删除客服帐号

```php
$staff->delete('foo@test');
```

### 设置客服帐号的头像

```php
$staff->avatar('foo@test', $avatarPath); // $avatarPath 为本地图片路径，非 URL
```

### 获取客服聊天记录 `NEW`

```php
$staff->records($startTime, $endTime, $pageIndex, $pageSize);

// example: $records = $staff->records('2015-06-07', '2015-06-21', 1, 20);
```

### 主动发送消息给用户

```php
$staff->message($message)->to($openId)->send();
```

> `$message` 为消息对象，请参考：[消息](messages.html)

### 指定客服发送消息

```php
$staff->message($message)->by('account@test')->to($openId)->send();
```
> `$message` 为消息对象，请参考：[消息](messages.html)

## 客服会话控制

> 客服会话为新版 API 功能

```php
<?php
use EasyWeChat\Foundation\Application;
// ...
$app = new Application($options);

$session = $app->staff_session; // 客服会话管理
```

## 创建会话

```php
$session->create('test1@test', 'OPENID');
```

### 关闭会话

```php
$session->close('test1@test', 'OPENID');
```

### 获取客户会话状态

```php
$session->get('OPENID');
```

### 获取客服会话列表

```php
$session->lists('test1@test');
```

### 获取未接入会话列表

```php
$session->waiters();
```


关于更多客服接口信息请参考微信官方文档：http://mp.weixin.qq.com/wiki
