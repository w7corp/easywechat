# 客户联系

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

### 批量获取客户详情

```php
$userId = 'zhangsai';
$cursor = '';
$limit = 100;

$app->external_contact->batchGetByUser(string $userId, string $cursor, int $limit);
```


### 修改客户备注信息

```php
$data  = [
    "userid"=>'员工id',
    "external_userid"=>'客户id',
    "remark"=> '新备注',
    "description"=>'新描述',
    "remark_company"=>'新公司',
    "remark_mobiles"=>[ '电话1','电话2'],
    "remark_pic_mediaid"=> "MEDIAID"
];

$app->external_contact->remark($data);
```



### 获取离职成员的客户列表

```php
$pageId = 0;
$pageSize = 1000;
$app->external_contact->getUnassigned($pageId, $pageSize);
```

### 分配成员的客户(离职或在职)

```php
$externalUserId = 'woAJ2GCAAAXtWyujaWJHDDGi0mACH71w';
$handoverUserId = 'zhangsan';
$takeoverUserId = 'lisi';
$transferSuccessMessage = '您好，您的服务已升级，后续将由我的同事张三@腾讯接替我的工作，继续为您服务。'; //不填则使用默认文案

$app->external_contact->transfer($externalUserId, $handoverUserId, $takeoverUserId, $transferSuccessMessage);
```


### 离职成员的群再分配

```php
$chatIds = ['群聊id1', '群聊id2'];
$takeoverUserId = '接替群主userid';

$app->external_contact->transferGroupChat($chatIds, $takeoverUserId);
```



### 查询客户接替结果

```php
$externalUserId = 'woAJ2GCAAAXtWyujaWJHDDGi0mACH71w';
$handoverUserId = 'zhangsan';
$takeoverUserId = 'lisi';

$app->external_contact->getTransferResult($externalUserId, $handoverUserId, $takeoverUserId);
```


## 客户群管理

### 获取客户群列表

```php
$params = [
    "status_filter" => 0,
    "owner_filter" => [
        "userid_list" => ["abel"],
        "partyid_list" => [7]
    ],
    "offset" => 0,
    "limit" => 100
];

$app->external_contact->getGroupChats(array $params);
```

### 获取客户群详情

```php
$chatId = 'wrOgQhDgAAMYQiS5ol9G7gK9JVAAAA';

$app->external_contact->getGroupChat(string $chatId);
```
## 客户朋友圈


### 获取企业全部的发表列表
```php
$params = [
    'start_time' => 1605000000,
    'end_time' => 1605172726,
    'creator' => 'zhangshan',
    'filter_type' => 1,
    'cursor' => 'CURSOR',
    'limit' => 10
];

$app->external_contact_moment->list(array $params);
```

### 获取客户朋友圈企业发表的列表

```php
$momentId = 'momxxx';
$cursor = 'CURSOR';
$limit = 10;

$app->external_contact_moment->getTasks(string $momentId, string $cursor, int $limit);
```

### 获取客户朋友圈发表时选择的可见范围

```php
$momentId = 'momxxx';
$userId = 'xxx';
$cursor = 'CURSOR';
$limit = 10;

$app->external_contact_moment->getCustomers(string $momentId, string $userId, string $cursor, int $limit);
```

### 获取客户朋友圈发表后的可见客户列表

```php
$momentId = 'momxxx';
$userId = 'xxx';
$cursor = 'CURSOR';
$limit = 10;

$app->external_contact_moment->getSendResult(string $momentId, string $userId, string $cursor, int $limit);
```

### 获取客户朋友圈的互动数据

```php
$momentId = 'momxxx';
$userId = 'xxx';

$app->external_contact_moment->getComments(string $momentId, string $userId);
```

## 客户标签管理

> 注意: 对于添加/删除/编辑企业客户标签接口，目前仅支持使用“客户联系”secret所获取的accesstoken来调用。
> 原文: https://work.weixin.qq.com/api/doc/90000/90135/92117

### 获取企业标签库

```php
$tagIds = [
    "etXXXXXXXXXX",
    "etYYYYYYYYYY"
];

$app->external_contact->getCorpTags(array $tagIds=[]);
```

### 添加企业客户标签

```php
$params = [
    "group_id" => "GROUP_ID",
    "group_name" => "GROUP_NAME",
    "order" => 1,
    "tag" => [
        [
            "name" => "TAG_NAME_1",
            "order" => 1
        ],
        [
            "name" => "TAG_NAME_2",
            "order" => 2
        ]
    ]
];

$app->external_contact->addCorpTag(array $params);
```


### 编辑企业客户标签

```php
$id = 'TAG_ID';
$name = 'NEW_TAG_NAME';
$order = 1;

$app->external_contact->updateCorpTag(string $id, string $name, int $order = 1);
```



### 删除企业客户标签

```php
$tagId = [
    'TAG_ID_1',
    'TAG_ID_2'
];
$groupId = [
    'GROUP_ID_1',
    'GROUP_ID_2'
];

$app->external_contact->deleteCorpTag(array $tagId,array $groupId);
```



### 编辑客户企业标签

```php
$params = [
    "userid" => "zhangsan",
    "external_userid" => "woAJ2GCAAAd1NPGHKSD4wKmE8Aabj9AAA",
    "add_tag" => ["TAGID1", "TAGID2"],
    "remove_tag" => ["TAGID3", "TAGID4"]
];

$app->external_contact->markTags(array $params);
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

###  获取「联系客户统计」数据

```php
$userIds = [
    'zhangsan',
    'lisi'
];
$partyIds = [
    'PARTY_ID_1',
    'PARTY_ID_2'
];
$from = 1536508800;
$to = 1536940800;

$app->external_contact_statistics->userBehavior($userIds, $from, $to, $partyIds);
```

###  获取「群聊数据统计」数据.

- 按群主聚合的方式

```php
$params = [
    'day_begin_time' => 1600272000,
    'day_end_time' => 1600444800,
    'owner_filter' => [
        'userid_list' => ['zhangsan']
    ],
    'order_by' => 2,
    'order_asc' => 0,
    'offset' => 0,
    'limit' => 1000
];

$app->external_contact_statistics->groupChatStatistic(array $params);
```

- 按自然日聚合的方式

```php
$dayBeginTime = 1600272000;
$dayEndTime = 1600444800;
$userIds = ['userid1', 'userid2'];

$app->external_contact_statistics->groupChatStatisticGroupByDay(int $dayBeginTime, int $dayEndTime, array $userIds);
```