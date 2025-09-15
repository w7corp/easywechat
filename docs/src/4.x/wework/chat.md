# 群聊管理

企业微信群聊管理功能允许应用管理企业内部群聊，包括创建群聊、管理群成员、发送群消息等。

## 获取实例

```php
$chat = $app->chat;
```

## 群聊基础操作

### 创建群聊

```php
$chatData = [
    'chatid' => 'project_team_001',
    'name' => '项目开发小组',
    'owner' => 'project_manager',
    'userlist' => ['dev001', 'dev002', 'test001', 'pm001']
];

$result = $chat->create($chatData);
```

### 获取群聊信息

```php
$result = $chat->get('project_team_001');
```

### 修改群聊信息

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

### 解散群聊

```php
$result = $chat->quit('project_team_001');
```

## 群成员管理

### 批量邀请成员

```php
$result = $chat->addMembers('project_team_001', ['new_dev001', 'new_qa001']);
```

### 批量移除成员

```php
$result = $chat->removeMembers('project_team_001', ['old_dev001', 'former_pm001']);
```

### 获取群成员列表

```php
$result = $chat->getMembers('project_team_001');
```

## 群消息发送

### 发送文本消息

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