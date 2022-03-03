# 外部联系人管理

## 获取实例

```php
$config = [
    'corp_id' => 'xxxxxxxxxxxxxxxxx',
    'secret'   => 'xxxxxxxxxx',
    ...
];

$app = Factory::work($config);

// 基础接口
$app->external_contact;

// 「联系我」
$app->contact_way;

// 消息管理
$app->external_contact_message;

// 数据统计
$app->external_contact_statistics;
```

## 基础接口

### 获取配置了客户联系功能的成员列表

```php
$app->external_contact->getFollowUsers();
```

### 获取外部联系人列表

```php
$userId = 'zhangsan';

$app->external_contact->list($userId);
```

### 获取外部联系人详情

```php
$externalUserId = 'woAJ2GCAAAXtWyujaWJHDDGi0mACH71w';

$app->external_contact->get($externalUserId);
```


### 获取离职成员的客户列表

```php
$pageId = 0;
$pageSize = 1000;
$app->external_contact->getUnassigned($pageId, $pageSize);
```

### 离职成员的外部联系人再分配

```php
$externalUserId = 'woAJ2GCAAAXtWyujaWJHDDGi0mACH71w';
$handoverUserId = 'zhangsan';
$takeoverUserId = 'lisi';

$app->external_contact->transfer($externalUserId, $handoverUserId, $takeoverUserId);
```


## 配置客户联系「联系我」方式

>  注意：
> 1. 通过API添加的「联系我」不会在管理端进行展示。
> 2. 每个企业可通过API最多配置10万个「联系我」。
> 3. 截止 2019-06-21 官方文档没有提供获取所有「联系我」列表的接口，请开发者注意自行保管处理 configId，避免无法溯源。

### 增加「联系我」方式

```php
$type = 1;
$scene = 1;
$config = [
   'style' => 1,
   'remark' => '渠道客户',
   'skip_verify' => true,
   'state' => 'teststate',
   'user' => ['UserID1', 'UserID2', 'UserID3'],
];

$app->contact_way->create($type, $scene, $config);

// {
//   "errcode": 0,
//   "errmsg": "ok",
//   "config_id":"42b34949e138eb6e027c123cba77fad7"　　
// }
```

### 获取「联系我」方式

```php
$configId = '42b34949e138eb6e027c123cba77fad7';

$app->contact_way->get($configId);
```

### 更新「联系我」方式

```php
$configId = '42b34949e138eb6e027c123cba77fad7';

$config = [
   'style' => 1,
   'remark' => '渠道客户2',
   'skip_verify' => true,
   'state' => 'teststate2',
   'user' => ['UserID4', 'UserID5', 'UserID6'],
];

$app->contact_way->update($configId, $config);
```

### 删除「联系我」方式

```php
$configId = '42b34949e138eb6e027c123cba77fad7';

$app->contact_way->delete($configId);
```

## 消息管理

### 添加企业群发消息模板

```php
$msg = [
    'external_userid' => [
        'woAJ2GCAAAXtWyujaWJHDDGi0mACas1w',
        'wmqfasd1e1927831291723123109r712',
    ],
    'sender' => 'zhangsan',
    'text' => [
        'content' => '文本消息内容',
    ],
    'image' => [
        'media_id' => 'MEDIA_ID',
    ],
    'link' => [
        'title' => '消息标题',
        'picurl' => 'https://example.pic.com/path',
        'desc' => '消息描述',
        'url' => 'https://example.link.com/path',
    ],
    'miniprogram' => [
        'title' => '消息标题',
        'pic_media_id' => 'MEDIA_ID',
        'appid' => 'wx8bd80126147df384',
        'page' => '/path/index',
    ],
];

$app->external_contact_message->submit($msg);

// {
//     "errcode": 0,
//     "errmsg": "ok",
//     "fail_list":["wmqfasd1e19278asdasdasd"],
//     "msgid":"msgGCAAAXtWyujaWJHDDGi0mACas1w"
// }
```

### 获取企业群发消息发送结果

```php
$msgId = 'msgGCAAAXtWyujaWJHDDGi0mACas1w';

$app->external_contact_message->get($msgId);
```

### 发送新客户欢迎语

```php
$welcomeCode = 'WELCOMECODE';

$msg = [
    'text' => [
        'content' => '文本消息内容',
    ],
    'image' => [
        'media_id' => 'MEDIA_ID',
    ],
    'link' => [
        'title' => '消息标题',
        'picurl' => 'https://example.pic.com/path',
        'desc' => '消息描述',
        'url' => 'https://example.link.com/path',
    ],
    'miniprogram' => [
        'title' => '消息标题',
        'pic_media_id' => 'MEDIA_ID',
        'appid' => 'wx8bd80126147df384',
        'page' => '/path/index',
    ],
];

$app->external_contact_message->sendWelcome($welcomeCode, $msg);
```


## 数据统计

### 获取员工行为数据

```php
$userIds = [
    'zhangsan',
    'lisi'
];

$from = 1536508800;
$to = 1536940800;

$app->external_contact_statistics->userBehavior($userIds, $from, $to);
```


