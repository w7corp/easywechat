# OA

```php
$config = [
    'corp_id' => 'xxxxxxxxxxxxxxxxx',
    'secret'   => 'xxxxxxxxxx',
    //...
];

$app = Factory::work($config);
```

## 打卡

### 获取企业所有打卡规则

```php
$app->oa->corpCheckinRules();
```

### 获取员工打卡规则

```php
$app->oa->checkinRules(int $datetime, array $userList);
```

### 获取打卡记录数据

> $type: 打卡类型 1：上下班打卡；2：外出打卡；3：全部打卡

```php

// 全部打卡数据
$app->oa->checkinRecords(1492617600, 1492790400, ["james","paul"]);

// 获取上下班打卡
$app->oa->checkinRecords(1492617600, 1492790400, ["james","paul"], 1);

// 获取外出打卡
$app->oa->checkinRecords(1492617600, 1492790400, ["james","paul"], 2);

```

### 获取打卡日报数据

```php
$app->oa->checkinDayData(int $startTime, int $endTime, array $userids);
```

### 获取打卡月报数据

```php
$app->oa->checkinMonthData(int $startTime, int $endTime, array $userids);
```

### 获取打卡人员排班信息

```php
 $params = [
            'groupid' => 226,
            'items' => [
                [
                    'userid' => 'james',
                    'day' => 5,
                    'schedule_id' => 234
                ]
            ],
            'yearmonth' => 202012
        ];
$app->oa->setCheckinSchedus(array $params);
```

### 为打卡人员排班

```php
$app->oa->checkinSchedus(int $startTime, int $endTime, array $userids);
```

### 录入打卡人员人脸信息

```php
$app->oa->addCheckinUserface(string $userid, string $userface)
```

## 获取审批数据

API:

```php
mixed approvalRecords(int $startTime, int $endTime, int $nextNumber = null)
```

> $nextNumber: 第一个拉取的审批单号，不填从该时间段的第一个审批单拉取

```php
$app->oa->approvalRecords(1492617600, 1492790400);

// 指定第一个拉取的审批单号，不填从该时间段的第一个审批单拉取
$app->oa->approvalRecords(1492617600, 1492790400, '201704240001');
```
