# 用户组


用户组的使用就非常简单了，基本的增删改查。

## 获取实例

```php
<?php
use EasyWeChat\Foundation\Application;

// ...

$app = new Application($options);

$group = $app->user_group; // $user['user_group']
```

## API

### 获取所有分组

```php
$group->lists();
```

example:

```php
$groups = $group->lists();

// {
//     "groups": [
//         {
//             "id": 0,
//             "name": "未分组",
//             "count": 72596
//         },
//         {
//             "id": 1,
//             "name": "黑名单",
//             "count": 36
//         },
//         ...
//     ]
// }

var_dump($groups->groups[0]['name']) // “未分组”
```

### 创建分组

```php
$group->create($name);
```

example:

```php
$group->create($name);
```

### 修改分组信息

```php
$group->update($groupId, $name);
```

example:

```php
$group->update($groupId, "新的组名");
```

### 删除分组

```php
$group->delete($groupId);
```

example:

```php
$group->delete($groupId);
```

### 移动单个用户到指定分组

```php
$group->moveUser($openId, $groupId);
```

example:

```php
$group->moveUser($openId, $groupId);
```

### 批量移动用户到指定分组

```php
$group->moveUsers(array $openIds, $groupId);
```

example:

```php
$openIds = [$openId1, $openId2, $openId3 ...];
$group->moveUsers($openIds, $groupId);
```

关于用户管理请参考微信官方文档：http://mp.weixin.qq.com/wiki/ `用户管理` 章节。
