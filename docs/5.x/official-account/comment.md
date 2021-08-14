# 评论数据管理



## 打开已群发文章评论

```php
$app->comment->open($msgId, $index = null);
```

## 关闭已群发文章评论

```php
$app->comment->close($msgId, $index = null);
```

## 查看指定文章的评论数据

```php
$app->comment->list(string $msgId, int $index, int $begin, int $count, int $type = 0);
```

## 将评论标记精选

```php
$app->comment->markElect(string $msgId, int $index, int $commentId);
```

## 将评论取消精选

```php
$app->comment->unmarkElect(string $msgId, int $index, int $commentId);
```

## 删除评论

```php
$app->comment->delete(string $msgId, int $index, int $commentId);
```

## 回复评论

```php
$app->comment->reply(string $msgId, int $index, int $commentId, string $content);
```

## 删除回复

```php
$app->comment->deleteReply(string $msgId, int $index, int $commentId);
```
