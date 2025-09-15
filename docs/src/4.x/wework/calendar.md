# 日程

企业微信日程功能允许应用管理企业内的日程安排，包括创建、更新、删除日程等操作。

## 获取实例

```php
$calendar = $app->calendar;
```

## 日程管理

### 创建日程

```php
$calendar = [
    'organizer' => 'organizer_userid',
    'summary' => '部门会议',
    'color' => 1,
    'description' => '讨论下季度工作计划',
    'shares' => [
        ['userid' => 'participant1'],
        ['userid' => 'participant2']
    ],
    'start_time' => 1635724800,  // 开始时间戳
    'end_time' => 1635728400,    // 结束时间戳
    'reminders' => [
        [
            'is_repeat' => 0,
            'remind_before_event_secs' => 300,  // 提前5分钟提醒
            'is_custom_repeat' => 0
        ]
    ],
    'location' => [
        'meetingroom' => '会议室A',
        'address' => '公司A座10楼会议室A'
    ]
];

$result = $calendar->add($calendar);
```

### 更新日程

```php
$calendarData = [
    'summary' => '更新后的会议标题',
    'description' => '更新后的会议内容',
    'start_time' => 1635731400,
    'end_time' => 1635735000
];

$result = $calendar->update('cal_id', $calendarData);
```

### 获取日程详情

```php
$result = $calendar->get('cal_id');
```

### 删除日程

```php
$result = $calendar->delete('cal_id');
```

### 获取日程列表

```php
$result = $calendar->list([
    'offset' => 0,
    'limit' => 10,
    'start_time' => 1635724800,  // 可选，开始时间
    'end_time' => 1635811200     // 可选，结束时间
]);
```

## 日程操作

### 接受日程邀请

```php
$result = $calendar->accept('cal_id');
```

### 拒绝日程邀请

```php
$result = $calendar->decline('cal_id');
```

### 设置日程状态

```php
$result = $calendar->setStatus('cal_id', [
    'status' => 1,  // 1:接受 2:拒绝 3:待定
    'userid' => 'participant1'
]);
```