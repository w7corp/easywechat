# OA

```php
$config = [
    'corp_id' => 'xxxxxxxxxxxxxxxxx',
    'secret'   => 'xxxxxxxxxx',
    //...
];

$app = Factory::work($config);
```

## 获取打卡数据

API:

```php
mixed checkinRecords(int $startTime, int $endTime, array $userList, int $type = 3)
```

> $type: 打卡类型 1：上下班打卡；2：外出打卡；3：全部打卡

```php
// 全部打卡数据
$app->oa->checkinRecords(1492617600, 1492790400, ["james","paul"]);

// 获取上下班打卡
$app->oa->checkinRecords(1492617600, 1492790400, ["james","paul"], 1);

// 获取外出打卡
$app->oa->checkinRecords(1492617600, 1492790400, ["james","paul"], 2);
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
