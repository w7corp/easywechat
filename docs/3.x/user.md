# 用户


用户信息的获取是微信开发中比较常用的一个功能了，以下所有的用户信息的获取与更新，都是**基于微信的 `openid` 的，并且是已关注当前账号的**，其它情况可能无法正常使用。

## 获取实例

```php
<?php
use EasyWeChat\Foundation\Application;

// ...

$app = new Application($options);

$userService = $app->user;
```

## API 列表

### 获取用户信息

```php
$userService->get($openId);
$userService->batchGet($openIds);
```

获取单个：

```php
$user = $userService->get($openId);

echo $user->nickname; // or $user['nickname']
```

获取多个：

```php
$users = $userService->batchGet([$openId1, $openId2, ...]);
```

### 获取用户列表

```php
$userService->lists($nextOpenId = null);  // $nextOpenId 可选
```

 example:

 ```php
 $users = $userService->lists();

 // result
 {
  "total": 2,
  "count": 2,
  "data": {
    "openid": [
      "",
      "OPENID1",
      "OPENID2"
    ]
  },
  "next_openid": "NEXT_OPENID"
}

$users->total; // 2
 ```

### 修改用户备注

```php
$userService->remark($openId, $remark); // 成功返回boolean
```

example:

```php
$userService->remark($openId, "僵尸粉");
```

### 获取用户所属用户组ID

```php
$userService->group($openId);
```

example:

```php
$userGroupId = $userService->group($openId);
```

## 其它

- [用户标签](user-tag.html)
- [用户分组](user-group.html)

关于用户管理请参考微信官方文档：http://mp.weixin.qq.com/wiki/ `用户管理` 章节。