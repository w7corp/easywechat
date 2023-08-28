## 文件上传  <version-tag>6.10.0+</version-tag>

由于微信 v3 对文件类上传使用特殊的签名机制，参见：[微信支付 - 图片上传API](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter2_1_1.shtml)。

因此，我们提供了一个媒体上传方法，方便开发者使用。

```php
$path = '/path/to/your/files/demo.jpg';

$api->uploadMedia('/v3/marketing/favor/media/image-upload', $path);
```

## 自定义 meta 信息

部分接口使用的签名 meta 不一致，所以可以自行传入：

```php
$url = '/v3/...';
$path = '/path/to/your/files/demo.jpg';
$meta = [
  'bank_type' => 'CFT',
  'filename' => 'demo.jpg',
  'sha256' => 'xxxxxxxxxxx',
];

$api->uploadMedia($url, $path, $meta);
```

## 关于 sha256

- 文件，用 `hash_file('sha256', $path)` 计算
- 字符串，用 `hash('sha256', $string)` 计算
