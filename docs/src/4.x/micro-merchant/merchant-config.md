# 小微商户配置

## 关注功能配置

```php
$response = $app->merchantConfig->setFollowConfig(string $subAppId, string $subscribeAppId, string $receiptAppId = '', string $subMchId = '');
```
> 注意：`subscribe_appid`，`receipt_appid` 两个参数二选一，两个都填的话SDK默认选第一个，具体请参考小微商户专属文档

## 开发配置新增支付目录

```php
$response = $app->merchantConfig->addPath(string $jsapiPath, string $appId = '', string $subMchId = '');
```

## 新增对应APPID关联

```php
$response = $app->merchantConfig->bindAppId(string $subAppId, string $appId = '', string $subMchId = '');
```

## 开发配置查询

```php
$response = $app->merchantConfig->getConfig(string $subMchId = '', string $appId = '');
```

> 以上接口调用过 `setSubMchId` 方法并且两个参数都传入过 则无需传入 `sub_mch_id` 和 `appid` 参数