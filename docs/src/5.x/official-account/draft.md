# 草稿箱

草稿箱提供了公众号文章的草稿管理功能，你可以通过草稿箱 API 添加、获取或删除草稿。

### 添加草稿

```php
use EasyWeChat\Kernel\Messages\Article;

// 添加单篇图文草稿
$article = new Article([
    'title' => 'xxx',
    'thumb_media_id' => $mediaId,
    'author' => 'overtrue',
    'show_cover' => 1,
    'digest' => '文章摘要',
    'content' => '文章内容',
    'source_url' => 'https://www.easywechat.com',
    //...
]);
$app->draft->add($article);

// 添加多篇图文草稿
$app->draft->add([$article, $article2, ...]);
```

### 获取草稿

```php
$app->draft->get($mediaId);
```

### 删除草稿

```php
$app->draft->delete($mediaId);
```

### 更新草稿

有三个参数：

> - `$mediaId` 要更新的草稿的 `mediaId`
> - `$article` 文章内容，`Article` 实例或者全字段数组
> - `$index` 要更新的文章在图文消息中的位置（多图文消息时，此字段才有意义，单图片忽略此参数），第一篇为 0

```php
$result = $app->draft->update($mediaId, new Article([
    'title' => 'EasyWeChat 5.x 发布了！',
    'thumb_media_id' => 'qQFxUQGO21Li4YrSn3MhnrqtRp9Zi3cbM9uBsepvDmE', // 封面图片 mediaId
    'author' => 'overtrue', // 作者
    'show_cover' => 1, // 是否在文章内容显示封面图片
    'digest' => '这里是文章摘要',
    'content' => '这里是文章内容，你可以放很长的内容',
    'source_url' => 'https://easywechat.com',
]));

// 指定更新多图文中的第 2 篇
$result = $app->draft->update($mediaId, new Article([...]), 1); // 第 2 篇
```

### 获取草稿总数

```php
$app->draft->count();
```

### 获取草稿列表

```php
$app->draft->batchGet($offset, $count, $noContent = 0);
```

> - `$offset` - 从全部素材的该偏移位置开始返回，可选，默认 `0`，0 表示从第一个素材返回
> - `$count` - 返回素材的数量，可选，默认 `20`，取值在 1 到 20 之间
> - `$noContent` - 1 表示不返回 content 字段，0 表示正常返回，默认为 0