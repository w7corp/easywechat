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

$server = Server::make($options);
$result = $server->run(); 

// 返回值$result为字符串，您可以直接用于echo 或者返回给框架
echo $result;
```

### 客户端

## License

MIT
