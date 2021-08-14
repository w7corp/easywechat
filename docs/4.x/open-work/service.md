# 第三方应用接口


## 获取预授权码

```php
$app->corp->getPreAuthCode();
```

## 设置授权配置

```php
$app->corp->setSession(string $preAuthCode, array $sessionInfo);
```

## 获取企业永久授权码

```php
$app->corp->getPermanentByCode(string $preAuthCode); //传入临时授权码
```

## 获取企业授权信息

```php
$app->corp->getAuthorization(string $authCorpId, string $permanentCode); //$authCorpId 授权的企业corp_id $permanentCode 授权的永久授权码
```

## 获取应用的管理员列表

```php
$app->corp->getManagers(string $authCorpId, string $agentId); //$authCorpId 授权的企业corp_id  $agentId 授权方安装的应用agentid
```

##  网页授权登录第三方

### 构造第三方oauth2链接

```php
//$redirectUri 回调uri 这里可以覆盖 默认读取配置文件
//$scope 应用授权作用域。
//$state 自定义安全值
$app->corp->getOAuthRedirectUrl(string $redirectUri = '', string $scope = 'snsapi_userinfo', string $state = null); 
```

### 第三方根据code获取企业成员信息

```php
$app->corp->getUserByCode(string $code); 
```

### 第三方使用user_ticket获取成员详情

```php
$app->corp->getUserByTicket(string $userTicket); 
```
