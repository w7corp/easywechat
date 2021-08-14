# 用户标签

## 获取所有标签

```php
$app->user_tag->list();
```

示例：

```php
$tags = $app->user_tag->list();

// {
//     "tags": [
//         {
//             "id": 0,
//             "name": "标签1",
//             "count": 72596
//         },
//         {
//             "id": 1,
//             "name": "标签2",
//             "count": 36
//         },
//         ...
//     ]
// }
```

## 创建标签

```php
$app->user_tag->create($name);
```

示例：

```php
$app->user_tag->create('测试标签');
```

## 修改标签信息

```php
$app->user_tag->update($tagId, $name);
```

示例：

```php
$app->user_tag->update(12, "新的名称");
```

## 删除标签

```php
$app->user_tag->delete($tagId);
```

## 获取指定 openid 用户所属的标签

```php
$userTags = $app->user_tag->userTags($openId);
//
// {
//     "tagid_list":["标签1","标签2"]
// }
```

## 获取标签下用户列表

```php
$app->user_tag->usersOfTag($tagId, $nextOpenId = '');
// $nextOpenId：第一个拉取的OPENID，不填默认从头开始拉取

// {
//   "count":2, // 这次获取的粉丝数量
//   "data":{ // 粉丝列表
//      "openid":[
//          "ocYxcuAEy30bX0NXmGn4ypqx3tI0",
//          "ocYxcuBt0mRugKZ7tGAHPnUaOW7Y"
//      ]
//   },
//   "next_openid":"ocYxcuBt0mRugKZ7tGAHPnUaOW7Y"//拉取列表最后一个用户的openid
// }
```

## 批量为用户添加标签

```php
$openIds = [$openId1, $openId2, ...];
$app->user_tag->tagUsers($openIds, $tagId);
```


## 批量为用户移除标签

```php
$openIds = [$openId1, $openId2, ...];
$app->user_tag->untagUsers($openIds, $tagId);
```
