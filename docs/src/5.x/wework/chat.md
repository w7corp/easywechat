# 群聊管理

企业微信群聊管理功能允许应用管理企业内部群聊，包括创建群聊、管理群成员、发送群消息等。

## 获取实例

```php
$chat = $app->chat;
```

## 群聊基础操作

### 创建群聊

创建新的企业微信群聊：

```php
$chatData = [
    'chatid' => 'project_team_001',
    'name' => '项目开发小组',
    'owner' => 'project_manager',
    'userlist' => ['dev001', 'dev002', 'test001', 'pm001']
];

$result = $chat->create($chatData);
```

**参数说明：**
- `chatid` string 群聊ID，可自定义，不能与已有群聊重复
- `name` string 群聊名称
- `owner` string 群主的userid
- `userlist` array 群成员userid列表

**返回结果：**
```json
{
    "errcode": 0,
    "errmsg": "ok"
}
```

### 获取群聊信息

获取指定群聊的详细信息：

```php
$result = $chat->get('project_team_001');
```

**返回结果：**
```json
{
    "errcode": 0,
    "errmsg": "ok",
    "chat_info": {
        "chatid": "project_team_001",
        "name": "项目开发小组",
        "owner": "project_manager",
        "userlist": [
            "dev001",
            "dev002", 
            "test001",
            "pm001"
        ]
    }
}
```

### 修改群聊信息

更新群聊的基本信息：

```php
$updateData = [
    'chatid' => 'project_team_001',
    'name' => 'Alpha项目开发小组',  // 新群名称
    'owner' => 'new_project_manager',  // 新群主
    'add_user_list' => ['dev003', 'qa001'],  // 新增成员
    'del_user_list' => ['test001']  // 移除成员
];

$result = $chat->update($updateData);
```

**参数说明：**
- `chatid` string 群聊ID
- `name` string 新群名称（可选）
- `owner` string 新群主userid（可选）
- `add_user_list` array 要添加的成员列表（可选）
- `del_user_list` array 要移除的成员列表（可选）

### 解散群聊

解散指定的群聊：

```php
$result = $chat->quit('project_team_001');
```

## 群成员管理

### 批量邀请成员

向群聊中批量添加成员：

```php
$result = $chat->addMembers('project_team_001', ['new_dev001', 'new_qa001', 'new_pm001']);
```

### 批量移除成员

从群聊中批量移除成员：

```php
$result = $chat->removeMembers('project_team_001', ['old_dev001', 'former_pm001']);
```

### 获取群成员列表

获取群聊的所有成员信息：

```php
$result = $chat->getMembers('project_team_001');
```

**返回结果：**
```json
{
    "errcode": 0,
    "errmsg": "ok",
    "userlist": [
        {
            "userid": "dev001",
            "status": 1,
            "join_time": 1635724800
        },
        {
            "userid": "pm001", 
            "status": 1,
            "join_time": 1635724800
        }
    ]
}
```

## 群消息发送

### 发送文本消息

向群聊发送文本消息：

```php
$message = [
    'chatid' => 'project_team_001',
    'msgtype' => 'text',
    'text' => [
        'content' => '大家好，项目进入关键阶段，请及时沟通进展情况。'
    ]
];

$result = $chat->sendMessage($message);
```

### 发送图片消息

发送图片消息到群聊：

```php
$message = [
    'chatid' => 'project_team_001',
    'msgtype' => 'image',
    'image' => [
        'media_id' => 'image_media_id_123'
    ]
];

$result = $chat->sendMessage($message);
```

### 发送文件消息

发送文件到群聊：

```php
$message = [
    'chatid' => 'project_team_001',
    'msgtype' => 'file',
    'file' => [
        'media_id' => 'file_media_id_456'
    ]
];

$result = $chat->sendMessage($message);
```

### 发送卡片消息

发送图文卡片消息：

```php
$message = [
    'chatid' => 'project_team_001',
    'msgtype' => 'news',
    'news' => [
        'articles' => [
            [
                'title' => '项目进度报告',
                'description' => '本周项目进展情况总结',
                'url' => 'https://work.weixin.qq.com/report/123',
                'picurl' => 'https://example.com/pic.jpg'
            ]
        ]
    ]
];

$result = $chat->sendMessage($message);
```

## 使用示例

### 项目群管理

```php
use EasyWeChat\Factory;

$config = [
    'corp_id' => 'your-corp-id',
    'agent_id' => 'your-agent-id',
    'secret' => 'your-secret',
    // ...
];

$app = Factory::work($config);
$chat = $app->chat;

// 1. 创建项目群
$projectChat = [
    'chatid' => 'alpha_project_' . date('Ymd'),
    'name' => 'Alpha项目组',
    'owner' => 'project_manager_001',
    'userlist' => [
        'pm_001',      // 项目经理
        'dev_001',     // 开发
        'dev_002', 
        'qa_001',      // 测试
        'ui_001',      // 设计
        'ops_001'      // 运维
    ]
];

$createResult = $chat->create($projectChat);

if ($createResult['errcode'] === 0) {
    $chatId = $projectChat['chatid'];
    echo "项目群创建成功: {$chatId}\n";
    
    // 2. 发送欢迎消息
    $welcomeMsg = [
        'chatid' => $chatId,
        'msgtype' => 'text',
        'text' => [
            'content' => "欢迎大家加入Alpha项目组！\n\n" .
                        "项目目标：开发新版本产品功能\n" .
                        "预计周期：8周\n" .
                        "请大家及时在群内同步工作进展，有问题随时讨论。"
        ]
    ];
    
    $sendResult = $chat->sendMessage($welcomeMsg);
    
    if ($sendResult['errcode'] === 0) {
        echo "欢迎消息发送成功\n";
    }
    
    // 3. 定期发送项目进度提醒
    $reminderMsg = [
        'chatid' => $chatId,
        'msgtype' => 'news',
        'news' => [
            'articles' => [
                [
                    'title' => '项目进度提醒',
                    'description' => '请各位同事及时更新项目进度，确保按时完成任务',
                    'url' => 'https://project.company.com/alpha/progress',
                    'picurl' => 'https://cdn.company.com/project-icon.png'
                ]
            ]
        ]
    ];
    
    $chat->sendMessage($reminderMsg);
}
```

### 动态群成员管理

```php
// 根据项目阶段动态调整群成员
function updateProjectTeam($chat, $chatId, $phase) {
    switch ($phase) {
        case 'design':
            // 设计阶段：加入设计师，移除运维
            $chat->addMembers($chatId, ['ui_002', 'ux_001']);
            $chat->removeMembers($chatId, ['ops_001']);
            
            $chat->sendMessage([
                'chatid' => $chatId,
                'msgtype' => 'text',
                'text' => [
                    'content' => '项目进入设计阶段，欢迎设计团队加入！'
                ]
            ]);
            break;
            
        case 'development':
            // 开发阶段：加入更多开发者
            $chat->addMembers($chatId, ['dev_003', 'dev_004', 'backend_001']);
            
            $chat->sendMessage([
                'chatid' => $chatId,
                'msgtype' => 'text',
                'text' => [
                    'content' => '项目进入开发阶段，开发团队全员到位！'
                ]
            ]);
            break;
            
        case 'testing':
            // 测试阶段：加入测试团队
            $chat->addMembers($chatId, ['qa_002', 'qa_003', 'automation_001']);
            
            $chat->sendMessage([
                'chatid' => $chatId,
                'msgtype' => 'text',
                'text' => [
                    'content' => '项目进入测试阶段，测试团队请开始工作！'
                ]
            ]);
            break;
            
        case 'deployment':
            // 部署阶段：重新加入运维
            $chat->addMembers($chatId, ['ops_001', 'ops_002']);
            
            $chat->sendMessage([
                'chatid' => $chatId,
                'msgtype' => 'text',
                'text' => [
                    'content' => '项目准备部署，运维团队请准备！'
                ]
            ]);
            break;
    }
}

// 使用示例
updateProjectTeam($chat, $chatId, 'development');
```

### 群消息推送系统

```php
// 定时推送项目状态
function sendProjectStatus($chat, $chatId) {
    // 获取项目数据（示例）
    $projectData = [
        'completed_tasks' => 45,
        'total_tasks' => 60,
        'bugs_fixed' => 12,
        'open_bugs' => 3,
        'progress' => 75
    ];
    
    $progressBar = str_repeat('█', intval($projectData['progress'] / 10)) . 
                   str_repeat('░', 10 - intval($projectData['progress'] / 10));
    
    $statusMessage = [
        'chatid' => $chatId,
        'msgtype' => 'text',
        'text' => [
            'content' => "📊 项目进度日报\n\n" .
                        "进度: {$progressBar} {$projectData['progress']}%\n" .
                        "任务完成: {$projectData['completed_tasks']}/{$projectData['total_tasks']}\n" .
                        "Bug修复: {$projectData['bugs_fixed']}\n" .
                        "待修复Bug: {$projectData['open_bugs']}\n\n" .
                        "继续加油！💪"
        ]
    ];
    
    return $chat->sendMessage($statusMessage);
}

// 每日定时发送
sendProjectStatus($chat, $chatId);
```

### 群聊数据统计

```php
// 统计群聊活跃度
function getChatStatistics($chat, $chatIds) {
    $statistics = [];
    
    foreach ($chatIds as $chatId) {
        $chatInfo = $chat->get($chatId);
        
        if ($chatInfo['errcode'] === 0) {
            $info = $chatInfo['chat_info'];
            $memberCount = count($info['userlist']);
            
            $statistics[] = [
                'chatid' => $chatId,
                'name' => $info['name'],
                'member_count' => $memberCount,
                'owner' => $info['owner']
            ];
        }
    }
    
    return $statistics;
}

// 生成群聊报告
$chatList = ['alpha_project_20231101', 'beta_project_20231015', 'gamma_project_20231020'];
$stats = getChatStatistics($chat, $chatList);

foreach ($stats as $stat) {
    echo "群聊: {$stat['name']}\n";
    echo "成员数: {$stat['member_count']}\n";
    echo "群主: {$stat['owner']}\n";
    echo "---\n";
}
```

## 注意事项

1. **群聊数量限制**：每个应用创建的群聊数量有限制
2. **成员数量限制**：单个群聊的成员数量有上限
3. **消息频率限制**：群消息发送有频率限制
4. **权限要求**：操作群聊需要相应的应用权限
5. **群主变更**：群主变更需要原群主或管理员权限

## 最佳实践

1. **群聊命名规范**：使用有意义的群聊ID和名称
2. **成员管理**：及时添加或移除相关成员
3. **消息内容**：发送有价值的群消息，避免垃圾信息
4. **权限控制**：合理设置群主和管理员
5. **定期清理**：定期清理不活跃的群聊

## 错误码说明

| 错误码 | 说明 |
|--------|------|
| 0 | 成功 |
| 40003 | 无效的UserID |
| 40013 | 不合法的CorpID |
| 86001 | 不合法的群聊ID |
| 86002 | 群聊不存在 |
| 86003 | 不合法的群聊名称 |
| 86004 | 群聊成员数量超出限制 |
| 86005 | 群聊创建数量超出限制 |