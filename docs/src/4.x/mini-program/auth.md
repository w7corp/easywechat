# 微信登录

## 根据 jsCode 获取用户 session 信息

```php
$result = $app->getUtils()->codeToSession($code);

// 返回结果：
// {
//     "openid": "OPENID",
//     "session_key": "SESSION_KEY",
//     "unionid": "UNIONID"  // 如果用户绑定了开放平台账号
// }
```

## 解密用户数据

当小程序需要获取用户敏感信息（如手机号、用户信息）时，使用此方法解密：

```php
$decryptedData = $app->getUtils()->decryptSession($sessionKey, $iv, $encryptedData);

// $sessionKey: 从 codeToSession 获取的 session_key
// $iv: 加密算法的初始向量
// $encryptedData: 加密的用户数据
```
