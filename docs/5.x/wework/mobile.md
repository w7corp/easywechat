# 移动端

## 通过code获取用户信息

通过iOS或Android应用授权登录，获取一次性code，通过后端服务器换取用户的信息。

```php
$code = 'CODE';

$app->mobile->getUser(string $code);
```