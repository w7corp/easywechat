# 微信开放平台第三方平台

此页涉及接口信息与说明请参见：[授权流程技术说明 - 官方文档](https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1453779503&token=&lang=)

# 微信开放平台第三方平台

## 实例化

```php
<?php
use EasyWeChat\Factory;

$config = [
  'app_id'   => '开放平台第三方平台 APPID',
  'secret'   => '开放平台第三方平台 Secret',
  'token'    => '开放平台第三方平台 Token',
  'aes_key'  => '开放平台第三方平台 AES Key'
];

$openPlatform = Factory::openPlatform($config);
```

## 获取用户授权页 URL

```php
$openPlatform->getPreAuthorizationUrl('https://easywechat.com/callback'); // 传入回调URI即可
```

## 使用授权码换取接口调用凭据和授权信息

在用户在授权页授权流程完成后，授权页会自动跳转进入回调URI，并在URL参数中返回授权码和过期时间，如：(https://easywechat.com/callback?auth_code=xxx&expires_in=600)

```php
$openPlatform->handleAuthorize(string $authCode = null);
```

> $authCode 不传的时候会获取 url 中的 auth_code 参数值

## 获取授权方的帐号基本信息

```php
$openPlatform->getAuthorizer(string $appId);
```

## 获取授权方的选项设置信息

```php
$openPlatform->getAuthorizerOption(string $appId, string $name);
```

## 设置授权方的选项信息

```php
$openPlatform->setAuthorizerOption(string $appId, string $name, string $value);
```

> 该API用于获取授权方的公众号或小程序的选项设置信息，如：地理位置上报，语音识别开关，多客服开关。注意，获取各项选项设置信息，需要有授权方的授权，详见权限集说明。


## 获取已授权的授权方列表

```php
$openPlatform->getAuthorizers(int $offset = 0, int $count = 500)
```
