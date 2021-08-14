# 短网址服务


主要使用场景： 开发者用于生成二维码的原链接（商品、支付二维码等）太长导致扫码速度和成功率下降，将原长链接通过此接口转成短链接再生成二维码将大大提升扫码速度和成功率。

## 获取实例

```php
<?php
use EasyWeChat\Foundation\Application;
// ...
$app = new Application($options);

$url = $app->url;
```

## API

+ `shorten($url)` 长链接转短链接

example:

```php
$shortUrl = $url->shorten('http://overtrue.me/open-source');
//
```

微信官方文档：http://mp.weixin.qq.com/wiki/
