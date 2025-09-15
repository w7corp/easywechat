# 二维码

目前有 2 种类型的二维码：

1. 临时二维码，是有过期时间的，最长可以设置为在二维码生成后的 **30天**后过期，但能够生成较多数量。临时二维码主要用于帐号绑定等不要求二维码永久保存的业务场景
2. 永久二维码，是无过期时间的，但数量较少（目前为最多10万个）。永久二维码主要用于适用于帐号绑定、用户来源统计等场景。

## 创建临时二维码

```php
$response = $app->getClient()->postJson('/cgi-bin/qrcode/create', [
    'expire_seconds' => 6 * 24 * 3600, // 6天后过期
    'action_name' => 'QR_STR_SCENE',
    'action_info' => [
        'scene' => [
            'scene_str' => 'foo'
        ]
    ]
]);

$result = $response->toArray();
// Array
// (
//     [ticket] => gQFD8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyTmFjVTRWU3ViUE8xR1N4ajFwMWsAAgS2uItZAwQA6QcA
//     [expire_seconds] => 518400
//     [url] => http://weixin.qq.com/q/02NacU4VSubPO1GSxj1p1k
// )
```

## 创建永久二维码

```php
// 永久字符串二维码
$response = $app->getClient()->postJson('/cgi-bin/qrcode/create', [
    'action_name' => 'QR_LIMIT_STR_SCENE',
    'action_info' => [
        'scene' => [
            'scene_str' => 'foo'
        ]
    ]
]);

// 永久数字二维码
$response = $app->getClient()->postJson('/cgi-bin/qrcode/create', [
    'action_name' => 'QR_LIMIT_SCENE',
    'action_info' => [
        'scene' => [
            'scene_id' => 56
        ]
    ]
]);

$result = $response->toArray();
// Array
// (
//     [ticket] => gQFD8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyTmFjVTRWU3ViUE8xR1N4ajFwMWsAAgS2uItZAwQA6QcA
//     [url] => http://weixin.qq.com/q/02NacU4VSubPO1GSxj1p1k
// )
```

## 获取二维码网址

```php
$url = "https://api.weixin.qq.com/cgi-bin/showqrcode?ticket=" . urlencode($ticket);
```

## 获取二维码内容

```php
$url = "https://api.weixin.qq.com/cgi-bin/showqrcode?ticket=" . urlencode($ticket);

$content = file_get_contents($url); // 得到二进制图片内容

file_put_contents(__DIR__ . '/code.jpg', $content); // 写入文件
```
