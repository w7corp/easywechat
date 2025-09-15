# 动态消息

小程序动态消息功能允许开发者创建可变更的消息内容，在特定条件下更新消息显示内容。

## 获取实例

```php
$activityMessage = $app->activity_message;
```

## 基础功能

### 创建动态消息活动ID

```php
$result = $activityMessage->createActivityId();
```

### 更新动态消息

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

### 参数说明

- `activityId`: 活动ID
- `state`: 目标状态：0-参与前状态 1-参与后状态
- `params`: 消息参数
  - `member_count`: 参与人数
  - `room_limit`: 房间人数上限  
  - `path`: 小程序页面路径
  - `version_type`: 版本类型

## 使用示例

```php
// 1. 创建活动ID
$activity = $activityMessage->createActivityId();

if ($activity['errcode'] === 0) {
    $activityId = $activity['activity_id'];
    
    // 2. 当房间状态变化时更新消息
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