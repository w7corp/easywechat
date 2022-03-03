# 多客服消息转发

多客服的消息转发绝对是超级的简单，转发的消息类型为 `transfer`：

```php

use EasyWeChat\Kernel\Messages\Transfer;

// 转发收到的消息给客服
$app->server->push(function($message) {
  return new Transfer();
});

$response = $app->server->serve();
```

当然，你也可以指定转发给某一个客服：

```php
use EasyWeChat\Kernel\Messages\Transfer;

$app->server->push(function($message) {
    return new Transfer($account);
});
```