# 服务商相关接口

## 单点登录


### 获取从第三方单点登录连接

```php
$app->provider->getLoginUrl(string $redirectUri = '', string $userType = 'admin', string $state = ''); //$redirectUri 回调地址  $userType支持登录的类型
```

### 获取登录用户信息

```php
$app->provider->getLoginInfo(string $authCode); //$authCode oauth2.0授权企业微信管理员登录产生的code，最长为512字节。只能使用一次，5分钟未被使用自动过期
```

## 注册定制化 

### 获取注册码

```php
$app->provider->getRegisterCode(
                        string $corpName = '', //企业名称
                        string $adminName = '',//管理员姓名
                        string $adminMobile = '',//管理员手机号
                        string $state = ''//自定义的状态值
                    ); 
```

### 获取注册Uri

```php
$app->provider->getRegisterUri(string $registerCode = ''); //$registerCode 注册码
```

### 查询注册状态

```php
$app->provider->getRegisterInfo(string $registerCode); //$registerCode 注册码
```

### 设置授权应用可见范围

```php
$app->provider->setAgentScope(
                        string $accessToken, //查询注册状态接口返回的access_token
                        string $agentId, //	授权方应用id
                        array $allowUser = [], //应用可见范围（成员）若未填该字段，则清空可见范围中成员列表
                        array $allowParty = [], //	应用可见范围（部门）若未填该字段，则清空可见范围中部门列表
                        array $allowTag = [] //应用可见范围（标签）若未填该字段，则清空可见范围中标签列表
                    )
```

### 设置通讯录同步完成

```php
$app->provider->contactSyncSuccess(string $accessToken); //$accessToken //查询注册状态接口返回的access_token
```
