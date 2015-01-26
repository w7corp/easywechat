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

### 客户端

## License

MIT
