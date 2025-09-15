# 商户功能

小程序商户功能允许小程序接入商户能力，为小程序提供商户服务相关的接口。

## 获取实例

```php
$business = $app->business;
```

## 商户注册

```php
$business->register('商户账号名', '商户昵称', '头像媒体ID');
```

## 获取商户信息

```php
// 通过商户ID获取
$business->getBusiness($businessId);

// 通过账号名获取
$business->getBusiness(0, 'account_name');
```

## 获取商户列表

```php
$business->list($offset, $count);
```

## 更新商户信息

```php
$business->update($businessId, '新昵称', '新头像媒体ID');
```

## 发送消息

```php
$message = [
    'touser' => 'openid',
    'msgtype' => 'text',
    'text' => [
        'content' => '消息内容'
    ],
    'business_id' => $businessId
];

$business->send($message);
```

## 设置输入状态

```php
$business->typing($businessId, $toUser);
```