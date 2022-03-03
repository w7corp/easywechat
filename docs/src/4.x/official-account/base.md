# 基础接口

## 清理接口调用次数

> 此接口官方有每月调用限制，不可随意调用

```php
$app->base->clearQuota();
```

## 获取微信服务器 IP (或IP段)

```php
$app->base->getValidIps();
```