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
    'app_id' => 'YOUR APP ID',
    'token'  => 'YOUR TOKEN',
    'AESKey' => 'YOUR AESKey' // optional
];

$server = new Server($options);
$server->setSecurityMode(Server::SEC_MODE_PLAIN_TEXT); //设置加解密方式

```

#### 微信的服务器验证
```php
$server->validation(); // 直接输出
```
如果你不想直接输出而是交给你的框架处理：
```php
$echoStr = $server->validation(true); // 把$_GET['echostr']返回
```

### 客户端

## License

MIT
