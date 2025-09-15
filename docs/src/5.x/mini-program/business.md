# 商户功能

小程序商户功能允许小程序接入商户能力，为小程序提供商户服务相关的接口。

## 获取实例

```php
$business = $app->business;
```

## 商户注册

注册商户账号：

```php
$result = $business->register('商户账号名', '商户昵称', '头像媒体ID');
```

**参数说明：**
- `accountName` string 商户账号名
- `nickname` string 商户昵称
- `iconMediaId` string 头像媒体ID

## 获取商户信息

可以通过商户ID或账号名获取商户信息：

```php
// 通过商户ID获取
$result = $business->getBusiness($businessId);

// 通过账号名获取
$result = $business->getBusiness(0, 'account_name');
```

**参数说明：**
- `businessId` int 商户ID
- `accountName` string 商户账号名

## 获取商户列表

获取商户列表，支持分页：

```php
$result = $business->list($offset, $count);
```

**参数说明：**
- `offset` int 偏移量，默认为0
- `count` int 返回数量，默认为10

## 更新商户信息

更新商户昵称和头像：

```php
$result = $business->update($businessId, '新昵称', '新头像媒体ID');
```

**参数说明：**
- `businessId` int 商户ID
- `nickname` string 新昵称（可选）
- `iconMediaId` string 新头像媒体ID（可选）

## 发送消息

### 构建消息

```php
$message = $business->message('消息内容');
```

### 发送消息

```php
$message = [
    'touser' => 'openid',
    'msgtype' => 'text',
    'text' => [
        'content' => '消息内容'
    ],
    'business_id' => $businessId
];

$result = $business->send($message);
```

## 设置输入状态

显示"正在输入"状态：

```php
$result = $business->typing($businessId, $toUser);
```

**参数说明：**
- `businessId` int 商户ID
- `toUser` string 用户openid

## 完整示例

```php
use EasyWeChat\Factory;

$config = [
    'app_id' => 'your-app-id',
    'secret' => 'your-app-secret',
    // ...
];

$app = Factory::miniProgram($config);
$business = $app->business;

// 注册商户
$result = $business->register('test_merchant', '测试商户', 'media_id_123');

if ($result['errcode'] === 0) {
    $businessId = $result['business_id'];
    
    // 发送消息
    $message = [
        'touser' => 'user_openid',
        'msgtype' => 'text',
        'text' => [
            'content' => '欢迎使用商户服务！'
        ],
        'business_id' => $businessId
    ];
    
    $business->send($message);
}
```