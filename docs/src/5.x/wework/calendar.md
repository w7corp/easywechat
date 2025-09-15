# 日程

企业微信日程功能允许应用管理企业内的日程安排，包括创建、更新、删除日程等操作。

## 获取实例

```php
$calendar = $app->calendar;
```

## 日程管理

### 创建日程

创建新的日程安排：

```php
$calendar = [
    'organizer' => 'organizer_userid',
    'summary' => '部门会议',
    'color' => 1,
    'description' => '讨论下季度工作计划',
    'shares' => [
        [
            'userid' => 'participant1'
        ],
        [
            'userid' => 'participant2'
        ]
    ],
    'start_time' => 1635724800,  // 开始时间戳
    'end_time' => 1635728400,    // 结束时间戳
    'cal_id' => '日程ID',
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

**参数说明：**
- `organizer` string 组织者的userid
- `summary` string 日程标题
- `color` int 日程颜色，0-11对应不同颜色
- `description` string 日程详情描述
- `shares` array 参与人列表
- `start_time` int 开始时间戳
- `end_time` int 结束时间戳
- `cal_id` string 日程ID（可选）
- `reminders` array 提醒设置
- `location` array 地点信息

**返回结果：**
```json
{
    "errcode": 0,
    "errmsg": "ok",
    "cal_id": "wcjgewCwAAqeJcPI1d8Pwbjt7nttzAAA"
}
```

### 更新日程

更新已存在的日程：

```php
$calendarData = [
    'summary' => '更新后的会议标题',
    'description' => '更新后的会议内容',
    'start_time' => 1635731400,
    'end_time' => 1635735000,
    'location' => [
        'meetingroom' => '会议室B',
        'address' => '公司B座5楼会议室B'
    ]
];

$result = $calendar->update('wcjgewCwAAqeJcPI1d8Pwbjt7nttzAAA', $calendarData);
```

### 获取日程详情

获取指定日程的详细信息：

```php
$result = $calendar->get('wcjgewCwAAqeJcPI1d8Pwbjt7nttzAAA');
```

**返回结果：**
```json
{
    "errcode": 0,
    "errmsg": "ok",
    "calendar": {
        "cal_id": "wcjgewCwAAqeJcPI1d8Pwbjt7nttzAAA",
        "organizer": "zhangsan",
        "summary": "部门会议",
        "color": 1,
        "description": "讨论下季度工作计划",
        "start_time": 1635724800,
        "end_time": 1635728400,
        "status": 1,
        "location": {
            "meetingroom": "会议室A",
            "address": "公司A座10楼会议室A"
        },
        "shares": [
            {
                "userid": "participant1"
            }
        ]
    }
}
```

### 删除日程

删除指定的日程：

```php
$result = $calendar->delete('wcjgewCwAAqeJcPI1d8Pwbjt7nttzAAA');
```

### 获取日程列表

获取指定时间范围内的日程列表：

```php
$result = $calendar->list([
    'offset' => 0,
    'limit' => 10,
    'start_time' => 1635724800,  // 可选，开始时间
    'end_time' => 1635811200     // 可选，结束时间
]);
```

**返回结果：**
```json
{
    "errcode": 0,
    "errmsg": "ok",
    "calendar_list": [
        {
            "cal_id": "wcjgewCwAAqeJcPI1d8Pwbjt7nttzAAA",
            "summary": "部门会议",
            "start_time": 1635724800,
            "end_time": 1635728400,
            "status": 1
        }
    ]
}
```

## 日程操作

### 接受日程邀请

参与者接受日程邀请：

```php
$result = $calendar->accept('wcjgewCwAAqeJcPI1d8Pwbjt7nttzAAA');
```

### 拒绝日程邀请

参与者拒绝日程邀请：

```php
$result = $calendar->decline('wcjgewCwAAqeJcPI1d8Pwbjt7nttzAAA');
```

### 设置日程状态

设置日程的参与状态：

```php
$result = $calendar->setStatus('wcjgewCwAAqeJcPI1d8Pwbjt7nttzAAA', [
    'status' => 1,  // 1:接受 2:拒绝 3:待定
    'userid' => 'participant1'
]);
```

## 使用示例

### 创建部门例会

```php
use EasyWeChat\Factory;

$config = [
    'corp_id' => 'your-corp-id',
    'agent_id' => 'your-agent-id',
    'secret' => 'your-secret',
    // ...
];

$app = Factory::work($config);
$calendar = $app->calendar;

// 创建每周例会
$meetingData = [
    'organizer' => 'manager_001',
    'summary' => '技术部周例会',
    'color' => 2,  // 蓝色
    'description' => '本周工作总结和下周计划讨论',
    'shares' => [
        ['userid' => 'dev_001'],
        ['userid' => 'dev_002'],
        ['userid' => 'dev_003']
    ],
    'start_time' => strtotime('next Monday 14:00'),
    'end_time' => strtotime('next Monday 15:30'),
    'reminders' => [
        [
            'is_repeat' => 0,
            'remind_before_event_secs' => 900,  // 提前15分钟提醒
            'is_custom_repeat' => 0
        ]
    ],
    'location' => [
        'meetingroom' => '技术部会议室',
        'address' => '研发中心2楼技术部会议室'
    ]
];

$result = $calendar->add($meetingData);

if ($result['errcode'] === 0) {
    $calId = $result['cal_id'];
    echo "例会创建成功，日程ID: {$calId}\n";
    
    // 获取日程详情确认
    $detail = $calendar->get($calId);
    if ($detail['errcode'] === 0) {
        $cal = $detail['calendar'];
        echo "会议标题: {$cal['summary']}\n";
        echo "开始时间: " . date('Y-m-d H:i:s', $cal['start_time']) . "\n";
        echo "结束时间: " . date('Y-m-d H:i:s', $cal['end_time']) . "\n";
        echo "参与人数: " . count($cal['shares']) . "\n";
    }
} else {
    echo "创建失败: {$result['errmsg']}\n";
}
```

### 批量处理日程

```php
// 获取本周的所有日程
$startOfWeek = strtotime('Monday this week');
$endOfWeek = strtotime('Sunday this week 23:59:59');

$calendarList = $calendar->list([
    'start_time' => $startOfWeek,
    'end_time' => $endOfWeek,
    'limit' => 50
]);

if ($calendarList['errcode'] === 0) {
    echo "本周共有 " . count($calendarList['calendar_list']) . " 个日程\n";
    
    foreach ($calendarList['calendar_list'] as $cal) {
        $startTime = date('m-d H:i', $cal['start_time']);
        $endTime = date('H:i', $cal['end_time']);
        
        echo "{$startTime}-{$endTime}: {$cal['summary']}\n";
        
        // 如果是会议室预订类型的会议，检查状态
        if (strpos($cal['summary'], '会议室') !== false) {
            $detail = $calendar->get($cal['cal_id']);
            if ($detail['errcode'] === 0) {
                $status = $detail['calendar']['status'] ?? 0;
                echo "  状态: " . ($status == 1 ? '正常' : '可能有冲突') . "\n";
            }
        }
    }
}
```

### 日程提醒管理

```php
// 创建有多种提醒的重要会议
$importantMeeting = [
    'organizer' => 'ceo_001',
    'summary' => '董事会会议',
    'color' => 0,  // 红色，表示重要
    'description' => '季度业绩汇报和战略讨论',
    'shares' => [
        ['userid' => 'cto_001'],
        ['userid' => 'cfo_001'],
        ['userid' => 'director_001']
    ],
    'start_time' => strtotime('+3 days 09:00'),
    'end_time' => strtotime('+3 days 12:00'),
    'reminders' => [
        [
            'is_repeat' => 0,
            'remind_before_event_secs' => 86400,  // 提前1天提醒
            'is_custom_repeat' => 0
        ],
        [
            'is_repeat' => 0,
            'remind_before_event_secs' => 3600,   // 提前1小时提醒
            'is_custom_repeat' => 0
        ],
        [
            'is_repeat' => 0,
            'remind_before_event_secs' => 300,    // 提前5分钟提醒
            'is_custom_repeat' => 0
        ]
    ],
    'location' => [
        'meetingroom' => '董事会议室',
        'address' => '总部大厦顶层董事会议室'
    ]
];

$result = $calendar->add($importantMeeting);
```

## 注意事项

1. **权限要求**：操作日程需要相应的应用权限
2. **时间格式**：所有时间参数使用Unix时间戳
3. **参与人限制**：单个日程的参与人数有上限
4. **日程冲突**：系统会检测时间冲突但不会自动解决
5. **提醒限制**：每个日程最多可设置5个提醒

## 最佳实践

1. **合理设置提醒**：根据会议重要性设置不同的提醒时间
2. **明确描述**：在description中提供详细的会议议程
3. **地点信息**：提供准确的会议地点信息
4. **状态管理**：及时更新和处理日程状态变更
5. **批量操作**：使用列表接口进行批量查询和管理

## 错误码说明

| 错误码 | 说明 |
|--------|------|
| 0 | 成功 |
| 40003 | 无效的UserID |
| 40013 | 不合法的CorpID |
| 60003 | 部门长度不符合限制 |
| 60102 | UserID已存在 |
| 85002 | 包含不合法字符 |
| 85004 | 每企业日程数量超过上限 |