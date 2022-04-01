# 会话内容存档

> 企业需要使用会话内容存档应用secret所获取的accesstoken来调用。
> 原文: https://work.weixin.qq.com/api/doc/90000/90135/91614


### 会话存档相关SDK

- [wework-msgaudit](https://github.com/aa24615/wework-msgaudit)


### 获取会话内容存档开启成员列表
```php
$type = 1;

$app->msg_audit->getPermitUsers(string $type);
```

### 获取会话同意情况

- 单聊

```php
$info = [
    [
        "userid" => "XuJinSheng1",
        "exteranalopenid" => "wmeDKaCQAAGd9oGiQWxVsAKwV2HxNAAA1"
    ],
    [
        "userid" => "XuJinSheng2",
        "exteranalopenid" => "wmeDKaCQAAGd9oGiQWxVsAKwV2HxNAAA2"
    ],
    [
        "userid" => "XuJinSheng3",
        "exteranalopenid" => "wmeDKaCQAAGd9oGiQWxVsAKwV2HxNAAA3"
    ]
];

$app->msg_audit->getSingleAgreeStatus(array $info);
```

- 群聊

```php
$roomId = 'wrjc7bDwAASxc8tZvBErFE02BtPWyAAA';

$app->msg_audit->getRoomAgreeStatus(string $roomId);
```

### 获取会话内容存档内部群信息

```php
$roomId = 'wrjc7bDwAASxc8tZvBErFE02BtPWyAAA';

$app->msg_audit->getRoom(string $roomId);
```



