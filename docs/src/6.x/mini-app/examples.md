# 示例

> 👏🏻 欢迎点击本页下方 "帮助我们改善此页面！" 链接参与贡献更多的使用示例！

<details>
    <summary>生成小程序码（wxacode.getUnlimited）</summary>

[官方文档：wxacode.getUnlimited](https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/qr-code/wxacode.getUnlimited.html)

```php
try {
    $response = $app->getClient()->postJson('/wxa/getwxacodeunlimit', [
        'scene' => '123',
        'page' => 'pages/index/index',
        'width' => 430,
        'check_path' => false,
    ]);
    
    $path = $response->saveAs('/tmp/wxacode-123.png');
]);
} catch (\Throwable $e) {
    // 失败
    echo $e->getMessage();
}
```
</details>

<!--
<details>
    <summary>标题</summary>
内容
</details>
-->
