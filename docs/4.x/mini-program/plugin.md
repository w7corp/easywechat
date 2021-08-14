# 插件管理

> 微信文档：https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/plugin-management/pluginManager.applyPlugin.html

## 申请使用插件

```php
$pluginAppId = 'xxxxxxxxx';

$app->plugin->apply($pluginAppId);
```

## 删除已添加的插件

```php
$pluginAppId = 'xxxxxxxxx';

$app->plugin->unbind($pluginAppId);
```

## 查询已添加的插件

```php
$app->plugin->list();
```

## 获取当前所有插件使用方

```php
$page = 1;
$size = 10;

$app->plugin_dev->getUsers($page, $size);
```

## 同意插件使用申请

```php
$appId = 'wxxxxxxxxxxxxxx';

$app->plugin_dev->agree($appId);
```

## 拒绝插件使用申请

```php
$app->plugin_dev->refuse('拒绝理由');
```

## 删除已拒绝的申请者

```php
$app->plugin_dev->delete();
```
