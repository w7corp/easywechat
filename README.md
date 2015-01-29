# Wechat
微信 SDK

## Install

```shell
composer require overtrue/wechat
```

## Usage

### 服务端

初始化

```php
<?php

use Overtrue\Wechat\Server;

$options = [
    'app_id'         => 'YOUR APP ID',
    'token'          => 'YOUR TOKEN',
    'encodingAESKey' => 'YOUR AESKey' 
];

$server = Server::make($options);
$result = $server->run(); 

// 返回值$result为字符串，您可以直接用于echo 或者返回给框架
echo $result;
```

#### 接收消息

语法 ：

```php
$server->message('消息类型', function($message){
    // $message 为微信请求过来的xml转换后的数组
});

```

sample:
```php
$server->message('text', function($message){
    error_log('收到文本消息：' . $message['Content']);
});
$server->message('image', function($message){
    error_log('收到图片消息：' . $message['PicUrl']);
});
$server->message('location', function($message){
    error_log('收到地址消息：' . $message['Label']);
});
$server->message('link', function($message){
    error_log('收到链接消息：' . $message['Url']);
});

$server->run();
```

#### 监听事件

语法：

```php
$server->event('事件类型', function($event){
    // $event 为微信请求过来的xml转换后的数组
});

```

sample:

```php
$server->event('unsubscribe', function($message){
    error_log('收到取消关注事件，取消关注者openid: ' . $message['FromUserName']);
});

$server->event('subscribe', function($message){
    error_log('收到关注事件, 关注者openid' . $message['FromUserName']);
});

$server->run();
```
### 客户端

初始化

```php
<?php

use Overtrue\Wechat\Client;

$options = [
    'app_id'     => 'YOUR APP ID',
    'app_secret' => 'YOUR APP SECRET',
];

$client = Client::make($options);

$client->error(function($error){
    // handle errors...
});
// ...
```

#### 自定义缓存写入/读取

```php
// writer
$client->cacheWriter(function($key, $value){
    // cache the value.
    return true;
});

// reader
$client->cacheReader(function($key){
    // return the cached value.
    return 缓存的数据;
});
```

## License

MIT
