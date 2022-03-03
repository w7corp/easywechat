# 用户标签


用户标签的使用就非常简单了，基本的增删改查。

## 获取实例

```php
<?php
use EasyWeChat\Foundation\Application;

// ...

$app = new Application($options);

$tag = $app->user_tag; // $user['user_tag']
```

## API

### 获取所有标签

```php
$tag->lists();
```

example:

```php
$tags = $tag->lists();

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

var_dump($tags->tags[0]['name']) // “标签1”
```

### 创建标签

```php
$tag->create($name);
```

example:

```php
$tag->create('测试标签');
```

### 修改标签信息

```php
$tag->update($tagId, $name);
```

example:

```php
$tag->update(12, "新的名称");
```

### 删除标签

```php
$tag->delete($tagId);
```

example:

```php
$tag->delete($tagId);
```

### 获取指定 openid 用户身上的标签

```php
$userTags = $tag->userTags($openId);
//
// {
//     "tagid_list":["标签1","标签2"]
// }
```

### 获取标签下粉丝列表

```php
$tag->usersOfTag($tagId, $nextOpenId = '');
// $nextOpenId：第一个拉取的OPENID，不填默认从头开始拉取

// {
//   "count":2,//这次获取的粉丝数量
//   "data":{//粉丝列表
//      "openid":[
//          "ocYxcuAEy30bX0NXmGn4ypqx3tI0",
//          "ocYxcuBt0mRugKZ7tGAHPnUaOW7Y"
//      ]
//   },
//   "next_openid":"ocYxcuBt0mRugKZ7tGAHPnUaOW7Y"//拉取列表最后一个用户的openid
// }
```

### 批量为用户打标签

```php
$openIds = [$openId1, $openId2, ...];
$tag->batchTagUsers($openIds, $tagId);
```


### 批量为用户取消标签

```php
$openIds = [$openId1, $openId2, ...];
$tag->batchUntagUsers($openIds, $tagId);
```

关于用户管理请参考微信官方文档：http://mp.weixin.qq.com/wiki/ `用户管理` 章节。
