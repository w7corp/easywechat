# 内容安全接口

## 文本安全内容检测

用于校验一段文本是否含有违法内容。

### 频率限制

单个appid调用上限为2000次/分钟，1,000,000次/天

### 调用示例

```php
// 传入要检测的文本内容，长度不超过500K字节
$content = '你好';

$result = $app->content_security->checkText($content);

// 正常返回 0
{
    "errcode": "0",
    "errmsg": "ok"
}

//当 $content 内含有敏感信息，则返回 87014
{
    "errcode": 87014,
    "errmsg": "risky content"
}
```

## 图片安全内容检测

用于校验一张图片是否含有敏感信息。如涉黄、涉及敏感人脸（通常是政治人物）。

### 频率限制

单个appid调用上限为1000次/分钟，100,000次/天

### 调用示例

```php
// 所传参数为要检测的图片文件的绝对路径，图片格式支持PNG、JPEG、JPG、GIF, 像素不超过 750 x 1334，同时文件大小以不超过 300K 为宜，否则可能报错
$result = $app->content_security->checkImage('/path/to/the/image');

// 正常返回 0
{
    "errcode": "0",
    "errmsg": "ok"
}

// 当图片文件内含有敏感内容，则返回 87014
{
    "errcode": 87014,
    "errmsg": "risky content"
}
```

## 重要说明

目前上述两个接口仅支持在小程序中使用，示例中的 `$app` 表示小程序实例，即:

```php
use EasyWeChat\Factory;

$config = [
    'app_id' => 'wx3cf0f39249eb0exx',
    'secret' => 'f1c242f4f28f735d4687abb469072axx',

    // 下面为可选项
    // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
    'response_type' => 'array',

    'log' => [
        'level' => 'debug',
        'file' => __DIR__.'/wechat.log',
    ],
];

$app = Factory::miniProgram($config);
```
