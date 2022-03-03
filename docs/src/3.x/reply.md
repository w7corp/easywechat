# 自动回复


## 获取实例

```php
<?php
use EasyWeChat\Foundation\Application;

// ...

$app = new Application($options);

$reply = $app->reply;
```

## 获取当前设置的回复规则

```php
$reply->current();
```