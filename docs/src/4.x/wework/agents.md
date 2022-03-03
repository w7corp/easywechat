# 应用管理

> 企业微信在 17 年 11 月对 API 进行了大量的改动，应用管理部分已经没啥用了

应用管理是企业微信中比较特别的地方，因为它的使用是不基于应用的，或者说基于任何一个应用都能访问这些 API，所以在用法上是直接调用 work 实例的 `agent` 属性。

```php
$config = [
    ...
];

$app = Factory::work($config);
```

## 应用列表

```php
$agents = $app->agent->list(); // 测试拿不到内容
```

## 应用详情

```php
$agents = $app->agent->get($agentId); // 只能传配置文件中的 id，API 改动所致
```

## 设置应用

```php
$agents = $app->agent->set($agentId, ['foo' => 'bar']);
```
