# 用户

用户信息的获取是微信开发中比较常用的一个功能了，以下所有的用户信息的获取与更新，都是**基于微信的 `openid` 的，并且是已关注当前账号的**，其它情况可能无法正常使用。

## 获取用户信息

获取单个：

```php
$user = $app->user->get($openId);
```

获取多个：

```php
$users = $app->user->select([$openId1, $openId2, ...]);
```

## 获取用户列表

```php
$app->user->list($nextOpenId = null);  // $nextOpenId 可选
```

示例：

```php
 $users = $app->user->list();

// result
 {
  "total": 2,
  "count": 2,
  "data": {
    "openid": [
      "OPENID1",
      "OPENID2"
    ]
  },
  "next_openid": "NEXT_OPENID"
}
```

## 修改用户备注

```php
$app->user->remark($openId, $remark); // 成功返回boolean
```

示例：

```php
$app->user->remark($openId, "僵尸粉");
```

## 拉黑用户

```php
$app->user->block('openidxxxxx');
// 或者多个用户
$app->user->block(['openid1', 'openid2', 'openid3', ...]);
```

## 取消拉黑用户

```php
$app->user->unblock('openidxxxxx');
// 或者多个用户
$app->user->unblock(['openid1', 'openid2', 'openid3', ...]);
```

## 获取黑名单

```php
$app->user->blacklist($beginOpenid = null); // $beginOpenid 可选
```

## 账号迁移 openid 转换

账号迁移请从这里了解：https://kf.qq.com/product/weixinmp.html#hid=2488

微信用户关注不同的公众号，对应的 OpenID 是不一样的，迁移成功后，粉丝的 OpenID 以目标帐号（即新公众号）对应的 OpenID 为准。但开发者可以通过开发接口转换 OpenID，开发文档可以参考：
提供一个 openid 转换的 API 接口，当帐号迁移后，可以通过该接口：

1. 将原帐号粉丝的 openid 转换为新帐号的 openid。
2. 将有授权关系用户的 openid 转换为新帐号的 openid。
3. 将卡券关联用户的 openid 转换为新帐号的 openid。

> - ◆ 原帐号：准备要迁移的帐号，当审核完成且管理员确认后即被回收。
> - ◆ 新帐号：用来接纳粉丝的帐号。新帐号在整个流程中均能正常使用。

一定要按照下面的步骤来操作。

1. 一定要在原帐号被冻结之前，最好是准备提交审核前，获取原帐号的用户列表。如果没有原帐号的用户列表，用不了转换工具。如果原账号被回收，这时候也没办法调用接口获取用户列表。

如何获取用户列表见这里：https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140840

2. 转换 openid 的 API 接口如下，可在帐号迁移审核完成后开始调用，并最多保留 15 天。若帐号迁移没完成，调用时无返回结果或报错。帐号迁移 15 天后，该转换接口将会失效、无法拉取到数据。

```php
$app->user->changeOpenid($oldAppId, $openidList);
```

返回值样例：

```json
{
  "errcode": 0,
  "errmsg": "ok",
  "result_list": [
    {
      "ori_openid": "oEmYbwN-n24jxvk4Sox81qedINkQ",
      "new_openid": "o2FwqwI9xCsVadFah_HtpPfaR-X4",
      "err_msg": "ok"
    },
    {
      "ori_openid": "oEmYbwH9uVd4RKJk7ZZg6SzL6tTo",
      "err_msg": "ori_openid error"
    }
  ]
}
```
