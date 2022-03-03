# 自定义菜单

自定义菜单是指为单个应用设置自定义菜单功能，所以在使用时请注意调用正确的应用实例。

```php
$config = [
    'corp_id' => 'xxxxxxxxxxxxxxxxx',
    'secret'   => 'xxxxxxxxxx', // 应用的 secret
    //...
];
$app = Factory::work($config);
```

## 创建菜单

```php
$menus = [
    'button' => [
        [
            'name' => '首页',
            'type' => 'view',
            'url' => 'https://easywechat.com'
        ],
        [
            'name' => '关于我们',
            'type' => 'view',
            'url' => 'https://easywechat.com/about'
        ],
        //...
    ],
];

$app->menu->create($menus);
```

## 获取菜单

```php
$app->menu->get();
```

## 删除菜单

```php
$app->menu->delete();
```
