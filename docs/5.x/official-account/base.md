# 基础接口

## 清理接口调用次数

> - 此接口官方有每月调用限制，不可随意调用
> - 每个帐号每月共10次清零操作机会，清零生效一次即用掉一次机会
> - 由于指标计算方法或统计时间差异，实时调用量数据可能会出现误差，一般在1%以内

```php
$app->base->clearQuota();
```

## 查询每日调用接口的额度和次数

> - `$cgiPath`是`api`的请求地址，例如`/cgi-bin/message/custom/send`，不要前缀`https://api.weixin.qq.com`，也不要漏了`/`
> - `/xxx/sns/xxx`这类接口不支持使用该接口，会出现76022报错
> - 接口调用次数的限制会在每天凌晨重置

```php
$app->base->getQuota($cgiPath);
```

示例：

```php
// 查询今天发送客服消息接口的额度和调用次数
$app->base->getQuota('/cgi-bin/message/custom/send');

// 查询今天发送模板消息接口的额度和调用次数
$app->base->getQuota('/cgi-bin/message/template/send');
```

## 获取微信服务器 IP (或IP段)

```php
$app->base->getValidIps();
```