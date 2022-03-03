# 微信客服

## 服务端(接收消息)
我们在企业微信 ”微信客服” 应用开启API接收消息的功能    
将设置页面的 token 与 aes key 配置到 agents 下对应的应用内   
> 注意: 需要使用“微信客服”secret所获取的accesstoken来调用
```php
$config = [
    'corp_id' => 'xxxxxxxxxxxxxxxxx',
    'secret'   => 'xxxxxxxxxx',
    // server config
    'token' => 'xxxxxxxxx',
    'aes_key' => 'xxxxxxxxxxxxxxxxxx',

    //...
];

$app = Factory::work($config);
```

接着配置服务端与公众号的服务端用法一样：

请参考微信客服文档 https://open.work.weixin.qq.com/api/doc/90000/90135/94670

```php
$app->server->push(function($message){
   // $message['FromUserName'] // 消息来源
   // $message['MsgType'] // 消息类型：event ....
    
    return 'Hello easywechat.';
});

$response = $app->server->serve();

$response->send();
```

`$response` 为 `Symfony\Component\HttpFoundation\Response` 实例，根据你的框架情况来决定如何处理响应。

## 客服帐号管理

### 添加客服帐号

```php
$app->kf_account->add(string $name, string $mediaId);
```

### 删除客服帐号

```php
$app->kf_account->del(string $openKfId);
```

### 修改客服帐号

```php
$app->kf_account->update(string $openKfId, string $name, string $mediaId);
```

### 获取客服帐号列表

```php
$app->kf_account->list();
```

### 获取客服帐号链接

```php
$app->kf_account->getAccountLink(string $openKfId, string $scene);
```

## 接待人员管理

### 添加接待人员

```php
$app->kf_servicer->add(string $openKfId, array $userIds);
```

### 删除接待人员

```php
$app->kf_servicer->del(string $openKfId, array $userIds);
```

### 获取接待人员列表

```php
$app->kf_servicer->list(string $openKfId);
```

## 会话分配与消息收发

### 获取会话状态

```php
$app->kf_message->state(string $openKfId, string $externalUserId);
```

### 变更会话状态

```php
$app->kf_message->updateState(string $openKfId, string $externalUserId, int $serviceState, string $serviceUserId);
```

### 读取消息

```php
$app->kf_message->sync(string $cursor, string $token, int $limit);
```

### 发送消息

```php
$app->kf_message->send(array $params);
```

### 发送事件响应消息

```php
$app->kf_message->event(array $params);
```