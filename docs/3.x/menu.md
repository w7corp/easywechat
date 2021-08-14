# 自定义菜单


3.0 的菜单组件有所简化，相比 2.x 版本变化如下：

- 去除 `MenuItem` 类，创建菜单直接使用数组不再支持 `callback` 与 `MenuItem` 类似的繁杂的方式
- `set()` 方法与 `addConditional()` 合并为一个方法 `add()`
- `get()` 改名为 `all()`
- `delete()` 与 `deleteById()` 合并为 `destroy()`
- 所有 API 的返回值（非调用失败情况）均为官方文档原样返回（Collection形式），不再取返回值中部分 `key` 返回。
  > 例如原来的 `get()` 方法，官方返回的数组为: `{ menu: [...]}`，SDK 取了其中的 `menu` 内容作为返回值，在 3.0 后将直接整体返回。

## 获取菜单模块实例

```php
<?php
use EasyWeChat\Foundation\Application;

// ...

$app = new Application($options);

$menu = $app->menu;
```

## API 列表

### 读取（查询）已设置菜单

微信的菜单读取有两个不同的方式：

一种叫 **[查询菜单](http://mp.weixin.qq.com/wiki/5/f287d1a5b78a35a8884326312ac3e4ed.html)**，在 SDK 中以 `all()` 方法来调用：

```php
$menus = $menu->all();
```

另外一种叫 **[获取自定义菜单](http://mp.weixin.qq.com/wiki/14/293d0cb8de95e916d1216a33fcb81fd6.html)**，使用 `current()` 方法来调用：

```php
$menus = $menu->current();
```

### 添加菜单

#### 添加普通菜单

```php
$buttons = [
    [
        "type" => "click",
        "name" => "今日歌曲",
        "key"  => "V1001_TODAY_MUSIC"
    ],
    [
        "name"       => "菜单",
        "sub_button" => [
            [
                "type" => "view",
                "name" => "搜索",
                "url"  => "http://www.soso.com/"
            ],
            [
                "type" => "view",
                "name" => "视频",
                "url"  => "http://v.qq.com/"
            ],
            [
                "type" => "click",
                "name" => "赞一下我们",
                "key" => "V1001_GOOD"
            ],
        ],
    ],
];
$menu->add($buttons);
```

以上将会创建一个普通菜单。

#### 添加个性化菜单

与创建普通菜单不同的是，需要在 `add()` 方法中将个性化匹配规则作为第二个参数传进去：

```php
$buttons = [
    // ...
];
$matchRule = [
    "tag_id" => "2",
    "sex" => "1",
    "country" => "中国",
    "province" => "广东",
    "city" => "广州",
    "client_platform_type" => "2",
    "language" => "zh_CN"
];
$menu->add($buttons, $matchRule);
```

### 删除菜单

有两种删除方式，一种是**全部删除**，另外一种是**根据菜单 ID 来删除**(删除个性化菜单时用，ID 从查询接口获取)：

```php
$menu->destroy(); // 全部
$menu->destroy($menuId);
```

### 测试个性化菜单

```php
$menus = $menu->test($userId);
```

> `$userId` 可以是粉丝的 OpenID，也可以是粉丝的微信号。

返回 `$menus` 与指定的 `$userId` 匹配的菜单项。

更多关于微信自定义菜单 API 请参考： http://mp.weixin.qq.com/wiki `自定义菜单` 章节。
