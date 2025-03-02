---
aside: false
title: 企业微信使用代码示例
---

# 示例

> 👏🏻 欢迎点击本页下方 "帮助我们改善此页面！" 链接参与贡献更多的使用示例！

<details open>
    <summary>被动回复一个图片信息</summary>

> [官方文档](https://developer.work.weixin.qq.com/document/path/90241)

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

</details>

<!--
<details>
    <summary>标题</summary>
内容
</details>
-->
