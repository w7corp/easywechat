# 动态消息

小程序动态消息功能允许开发者创建可变更的消息内容，在特定条件下更新消息显示内容。

## 获取实例

```php
$activityMessage = $app->activity_message;
```

## 基础功能

### 创建动态消息活动ID

创建用于发送动态消息的活动ID：

```php
$result = $activityMessage->createActivityId();
```

**返回结果：**
```json
{
    "errcode": 0,
    "errmsg": "ok",
    "activity_id": "xxx",
    "expiration_time": 1635724800
}
```

**参数说明：**
- `activity_id` string 活动ID，用于后续消息更新
- `expiration_time` int 活动过期时间戳

### 更新动态消息

更新已发送的动态消息内容：

```php
$params = [
    'member_count' => 2,      // 参与人数
    'room_limit' => 4,        // 房间人数上限
    'path' => 'pages/room?room_id=123',  // 页面路径
    'version_type' => 'develop'  // 版本类型：develop, trial, release
];

$result = $activityMessage->updateMessage(
    'activity_id_xxx',  // 活动ID
    1,                  // 目标状态：0-参与前 1-参与后
    $params             // 更新参数
);
```

**参数说明：**
- `activityId` string 活动ID
- `state` int 目标状态：0-参与前状态 1-参与后状态
- `params` array 消息参数
  - `member_count` int 参与人数
  - `room_limit` int 房间人数上限  
  - `path` string 小程序页面路径
  - `version_type` string 版本类型

**返回结果：**
```json
{
    "errcode": 0,
    "errmsg": "ok"
}
```

## 使用场景

### 游戏房间动态消息

```php
use EasyWeChat\Factory;

$config = [
    'app_id' => 'your-app-id',
    'secret' => 'your-app-secret',
    // ...
];

$app = Factory::miniProgram($config);
$activityMessage = $app->activity_message;

// 1. 创建活动ID
$activity = $activityMessage->createActivityId();

if ($activity['errcode'] === 0) {
    $activityId = $activity['activity_id'];
    echo "活动ID创建成功: {$activityId}\n";
    
    // 2. 用户发送消息时附带活动ID
    // 在发送消息接口中使用 activity_id
    
    // 3. 当房间状态变化时更新消息
    // 例如：有新用户加入房间
    $updateParams = [
        'member_count' => 3,  // 当前3人
        'room_limit' => 4,    // 最多4人
        'path' => 'pages/game/room?id=room_123'
    ];
    
    $updateResult = $activityMessage->updateMessage($activityId, 1, $updateParams);
    
    if ($updateResult['errcode'] === 0) {
        echo "消息更新成功，房间现在有3人\n";
    }
}
```

### 拼团活动动态消息

```php
// 创建拼团活动的动态消息
$activity = $activityMessage->createActivityId();

if ($activity['errcode'] === 0) {
    $activityId = $activity['activity_id'];
    
    // 模拟拼团过程中的状态更新
    $groupBuyingStates = [
        ['member_count' => 1, 'room_limit' => 5, 'state' => 0], // 发起拼团
        ['member_count' => 3, 'room_limit' => 5, 'state' => 0], // 3人参与
        ['member_count' => 5, 'room_limit' => 5, 'state' => 1], // 拼团成功
    ];
    
    foreach ($groupBuyingStates as $index => $stateData) {
        $params = [
            'member_count' => $stateData['member_count'],
            'room_limit' => $stateData['room_limit'],
            'path' => 'pages/group-buy/detail?group_id=gb_123'
        ];
        
        $result = $activityMessage->updateMessage($activityId, $stateData['state'], $params);
        
        if ($result['errcode'] === 0) {
            echo "拼团状态更新: {$stateData['member_count']}/{$stateData['room_limit']}人\n";
        }
        
        // 模拟时间间隔
        sleep(1);
    }
}
```

### 实时竞赛动态消息

```php
// 创建竞赛活动的动态消息
$activity = $activityMessage->createActivityId();

if ($activity['errcode'] === 0) {
    $activityId = $activity['activity_id'];
    
    // 竞赛报名阶段
    $registrationParams = [
        'member_count' => 8,   // 已报名8人
        'room_limit' => 20,    // 最多20人
        'path' => 'pages/contest/detail?contest_id=c_123'
    ];
    
    $activityMessage->updateMessage($activityId, 0, $registrationParams);
    echo "竞赛报名中: 8/20人\n";
    
    // 竞赛开始阶段
    $startParams = [
        'member_count' => 20,  // 满员开始
        'room_limit' => 20,
        'path' => 'pages/contest/live?contest_id=c_123'
    ];
    
    $activityMessage->updateMessage($activityId, 1, $startParams);
    echo "竞赛开始: 20/20人 已开始\n";
}
```

### 直播间动态消息

```php
// 直播间观众数量变化
$activity = $activityMessage->createActivityId();

if ($activity['errcode'] === 0) {
    $activityId = $activity['activity_id'];
    
    // 模拟直播间观众数量变化
    $viewerCounts = [10, 25, 50, 100, 250];
    
    foreach ($viewerCounts as $count) {
        $params = [
            'member_count' => $count,
            'room_limit' => 1000,  // 直播间容量
            'path' => 'pages/live/room?live_id=live_123'
        ];
        
        // 根据观众数量决定状态
        $state = $count >= 100 ? 1 : 0;  // 超过100人为热门状态
        
        $result = $activityMessage->updateMessage($activityId, $state, $params);
        
        if ($result['errcode'] === 0) {
            $status = $state ? '🔥热门' : '📺直播中';
            echo "直播间更新: {$status} {$count}人观看\n";
        }
        
        sleep(2); // 模拟时间间隔
    }
}
```

### 队伍组建动态消息

```php
function updateTeamMessage($activityMessage, $activityId, $currentMembers, $maxMembers, $teamId) {
    $params = [
        'member_count' => $currentMembers,
        'room_limit' => $maxMembers,
        'path' => "pages/team/detail?team_id={$teamId}"
    ];
    
    // 队伍满员时切换到完成状态
    $state = ($currentMembers >= $maxMembers) ? 1 : 0;
    
    $result = $activityMessage->updateMessage($activityId, $state, $params);
    
    if ($result['errcode'] === 0) {
        $status = $state ? '✅已满员' : '🚀招募中';
        echo "队伍状态: {$status} {$currentMembers}/{$maxMembers}人\n";
        return true;
    }
    
    return false;
}

// 使用示例
$activity = $activityMessage->createActivityId();
if ($activity['errcode'] === 0) {
    $activityId = $activity['activity_id'];
    
    // 模拟队伍成员逐渐加入
    for ($i = 1; $i <= 5; $i++) {
        updateTeamMessage($activityMessage, $activityId, $i, 5, 'team_abc123');
        sleep(1);
    }
}
```

## 注意事项

1. **活动ID有效期**：活动ID有过期时间，过期后无法更新消息
2. **更新频率限制**：消息更新有频率限制，不要过于频繁调用
3. **状态一致性**：确保传递的参数与实际业务状态一致
4. **页面路径有效性**：确保path参数指向的页面存在且可访问
5. **版本类型**：根据小程序发布状态选择正确的version_type

## 最佳实践

1. **合理使用场景**：动态消息适用于多人协作、实时状态变化的场景
2. **状态管理**：清晰定义参与前后的状态差异
3. **用户体验**：确保消息更新能够提供有价值的信息
4. **错误处理**：做好活动ID过期和更新失败的处理
5. **数据同步**：保持消息内容与实际业务数据同步

## 错误码说明

| 错误码 | 说明 |
|--------|------|
| 0 | 成功 |
| -1 | 系统繁忙，此时请开发者稍候再试 |
| 40001 | 获取access_token时AppSecret错误 |
| 40013 | 不合法的AppID |
| 41001 | 缺少access_token参数 |
| 45009 | 接口调用超过限额 |
| 47001 | 参数错误 |
| 47501 | 一天只能创建100个活动ID |
| 47502 | 活动ID已过期 |
| 47503 | 状态值错误 |