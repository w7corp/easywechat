# 微信小程序消息解密

## 比如获取电话等功能，信息是加密的，需要解密。

API:

```php
$decryptedData = $app->encryptor->decryptData($session, $iv, $encryptedData);
```
