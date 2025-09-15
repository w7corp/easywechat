# 基础接口

## 清理接口调用次数

> 此接口官方有每月调用限制，不可随意调用

```php
$response = $app->getClient()->post('/cgi-bin/clear_quota');
```

## 获取微信服务器 IP (或IP段)

```php
$response = $app->getClient()->get('/cgi-bin/getcallbackip');
$ips = $response->toArray();
```