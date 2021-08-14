# 企业互联

### 获取应用共享信息

```php
$agentId = 100001;

$app->msg_audit->getAppShareInfo(int $agentId);
```

### 获取下级企业的access_token

```php
$corpId = 'wwd216fa8c4c5c0e7x';
$agentId = 100001;

$app->msg_audit->getToken(string $corpId, int $agentId)
```

### 获取下级企业的小程序session


```php
$userId = 'wmAoNVCwAAUrSqEqz7oQpEIEMVWDrPeg';
$sessionKey = 'n8cnNEoyW1pxSRz6/Lwjwg==';

$app->msg_audit->getMiniProgramTransferSession(string $userId, string $sessionKey);
```
