# 客服

使用客服系统可以向用户发送消息以及群发消息，客服的管理等功能。

## 客服管理

### 获取所有客服

```php
$app->customer_service->list();
```

### 获取所有在线的客服

```php
$app->customer_service->online();
```

### 添加客服

```php
$app->customer_service->create('foo@test', '客服1');
```

### 修改客服

```php
$app->customer_service->update('foo@test', '客服1');
```

### 删除账号

```php
$app->customer_service->delete('foo@test');
```

### 设置客服头像

```php
$app->customer_service->setAvatar('foo@test', $avatarPath); // $avatarPath 为本地图片路径，非 URL
```

### 获取客服与客户聊天记录

```php
$app->customer_service->messages($startTime, $endTime, $msgId = 1, $number = 10000);
```

示例:

```php
$records = $app->customer_service->messages('2015-06-07', '2015-06-21', 1, 20000);
```

### 主动发送消息给用户

```php
$app->customer_service->message($message)->to($openId)->send();
```

> `$message` 为消息对象或文本，请参考：[消息](messages)

示例：

```php
$app->customer_service->message('hello')
                  >  ->to('oV-gpwdOIwSI958m9osAhGBFxxxx')
                  >  ->send();
```

### 指定客服发送消息

```php
$app->customer_service->message($message)
                      >  ->from('account@test')
                      >  ->to($openId)
                      >  ->send();
```
> `$message` 为消息对象或文本，请参考：[消息](messages.html)

示例：

```php
$app->customer_service->message('hello')
                  >  ->from('kf2001@gh_176331xxxx')
                  >  ->to('oV-gpwdOIwSI958m9osAhGBFxxxx')
                  >  ->send();
```

### 邀请微信用户加入客服

以账号 `foo@test` 邀请 微信号 为 `xxxx` 的微信用户加入客服。

```php
$app->customer_service->invite('foo@test', 'xxxx');
```

## 客服会话控制

## 创建会话

```php
$app->customer_service_session->create('test1@test', 'OPENID');
```

### 关闭会话

```php
$app->customer_service_session->close('test1@test', 'OPENID');
```

### 获取客户会话状态

```php
$app->customer_service_session->get('OPENID');
```

### 获取客服会话列表

```php
$app->customer_service_session->list('test1@test');
```

### 获取未接入会话列表

```php
$app->customer_service_session->waiting();
```
