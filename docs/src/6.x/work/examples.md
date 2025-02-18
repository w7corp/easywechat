# 示例

> 👏🏻 欢迎点击本页下方 "帮助我们改善此页面！" 链接参与贡献更多的使用示例！

## [被动回复](https://developer.work.weixin.qq.com/document/path/90241) {#server-mode}

::: details 被动回复一个图片信息 {open .bg-transparent}

```php
$server->with(function ($message) {
    return [
        'MsgType' => 'image',
        'Image' => [
            'MediaId' => $message['MediaId'],
        ],
    ]);
};
```

`$server` 见[这里](server)，`media_id` 需提前由 [企业微信>素材管理](https://developer.work.weixin.qq.com/document/path/91054) 接口产生。

:::
