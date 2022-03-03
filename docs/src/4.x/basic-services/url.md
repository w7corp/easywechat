# 短网址服务

主要使用场景： 开发者用于生成二维码的原链接（商品、支付二维码等）太长导致扫码速度和成功率下降，将原长链接通过此接口转成短链接再生成二维码将大大提升扫码速度和成功率。

## 长链接转短链接

```php
$shortUrl = $app->url->shorten('https://easywechat.com');
//
(
    [errcode] => 0
    [errmsg] => ok
    [short_url] => https://w.url.cn/s/Aq7jWrd
)
```