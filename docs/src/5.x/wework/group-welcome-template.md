# 入群欢迎语

企业微信入群欢迎语功能允许企业管理外部联系人入群时的欢迎消息模板，提升客户体验。

## 获取实例

```php
$groupWelcomeTemplate = $app->group_welcome_template;
```

## 欢迎语模板管理

### 添加入群欢迎语素材

向企业的入群欢迎语素材库中添加新的素材：

```php
$templateData = [
    'text' => [
        'content' => '欢迎加入我们的产品交流群！\n\n我是您的专属客服小助手，有任何问题都可以随时咨询我。'
    ],
    'image' => [
        'media_id' => 'image_media_id_123',
        'pic_url' => 'https://example.com/welcome.jpg'
    ],
    'link' => [
        'title' => '产品使用指南',
        'picurl' => 'https://example.com/guide_thumb.jpg',
        'desc' => '点击查看产品详细使用说明',
        'url' => 'https://help.example.com/guide'
    ],
    'miniprogram' => [
        'title' => '产品小程序',
        'pic_media_id' => 'pic_media_id_456',
        'appid' => 'wx1234567890abcdef',
        'page' => 'pages/welcome/index'
    ]
];

$result = $groupWelcomeTemplate->add($templateData);
```

**参数说明：**
- `text` array 文本消息内容
- `image` array 图片消息（可选）
- `link` array 链接消息（可选）
- `miniprogram` array 小程序消息（可选）

**返回结果：**
```json
{
    "errcode": 0,
    "errmsg": "ok",
    "template_id": "msgtemplate4doGWjViuUW"
}
```

### 编辑入群欢迎语素材

编辑已存在的入群欢迎语素材：

```php
$templateId = 'msgtemplate4doGWjViuUW';
$updateData = [
    'text' => [
        'content' => '欢迎加入我们的VIP客户交流群！\n\n感谢您对我们产品的支持，我们将为您提供专属的优质服务。'
    ],
    'image' => [
        'media_id' => 'new_image_media_id',
        'pic_url' => 'https://example.com/vip_welcome.jpg'
    ]
];

$result = $groupWelcomeTemplate->edit($templateId, $updateData);
```

### 获取入群欢迎语素材

获取指定的入群欢迎语素材详情：

```php
$result = $groupWelcomeTemplate->get('msgtemplate4doGWjViuUW');
```

**返回结果：**
```json
{
    "errcode": 0,
    "errmsg": "ok",
    "text": {
        "content": "欢迎加入我们的产品交流群！"
    },
    "image": {
        "pic_url": "https://example.com/welcome.jpg"
    },
    "link": {
        "title": "产品使用指南",
        "picurl": "https://example.com/guide_thumb.jpg",
        "desc": "点击查看产品详细使用说明",
        "url": "https://help.example.com/guide"
    }
}
```

### 删除入群欢迎语素材

删除指定的入群欢迎语素材：

```php
$result = $groupWelcomeTemplate->delete('msgtemplate4doGWjViuUW');
```

## 使用示例

### 创建多样化欢迎语模板

```php
use EasyWeChat\Factory;

$config = [
    'corp_id' => 'your-corp-id',
    'agent_id' => 'your-agent-id',
    'secret' => 'your-secret',
    // ...
];

$app = Factory::work($config);
$groupWelcomeTemplate = $app->group_welcome_template;

// 1. 基础文本欢迎语
$basicTemplate = [
    'text' => [
        'content' => '👋 欢迎加入我们的官方客户群！\n\n' .
                    '🎯 群功能介绍：\n' .
                    '• 产品使用答疑\n' .
                    '• 新功能抢先体验\n' .
                    '• 专享优惠活动\n\n' .
                    '💡 有问题随时 @我，1对1为您解答'
    ]
];

$basicResult = $groupWelcomeTemplate->add($basicTemplate);

if ($basicResult['errcode'] === 0) {
    echo "基础欢迎语创建成功: {$basicResult['template_id']}\n";
}

// 2. 富媒体欢迎语（图片+链接）
$richTemplate = [
    'text' => [
        'content' => '🎉 欢迎加入VIP专享群！'
    ],
    'image' => [
        'pic_url' => 'https://cdn.example.com/vip_welcome.png'
    ],
    'link' => [
        'title' => 'VIP专享权益说明',
        'picurl' => 'https://cdn.example.com/vip_benefits_thumb.jpg',
        'desc' => '点击了解VIP会员专享服务内容',
        'url' => 'https://vip.example.com/benefits'
    ]
];

$richResult = $groupWelcomeTemplate->add($richTemplate);

if ($richResult['errcode'] === 0) {
    echo "富媒体欢迎语创建成功: {$richResult['template_id']}\n";
}

// 3. 小程序欢迎语
$miniprogramTemplate = [
    'text' => [
        'content' => '欢迎使用我们的小程序服务！'
    ],
    'miniprogram' => [
        'title' => '立即体验产品',
        'appid' => 'wx1234567890abcdef',
        'page' => 'pages/newuser/welcome?from=group',
        'pic_media_id' => 'miniprogram_pic_media_id'
    ]
];

$miniprogramResult = $groupWelcomeTemplate->add($miniprogramTemplate);

if ($miniprogramResult['errcode'] === 0) {
    echo "小程序欢迎语创建成功: {$miniprogramResult['template_id']}\n";
}
```

### 分类管理欢迎语模板

```php
// 根据不同群类型创建不同的欢迎语
function createWelcomeTemplatesByType($groupWelcomeTemplate) {
    $templates = [
        'product_support' => [
            'text' => [
                'content' => '🔧 欢迎加入产品技术支持群！\n\n' .
                           '📋 群内服务：\n' .
                           '• 技术问题快速解答\n' .
                           '• 产品使用教程分享\n' .
                           '• 故障排除指导\n\n' .
                           '⏰ 服务时间：工作日 9:00-18:00'
            ],
            'link' => [
                'title' => '技术文档中心',
                'desc' => '查看完整的产品技术文档',
                'url' => 'https://docs.example.com',
                'picurl' => 'https://cdn.example.com/docs_thumb.jpg'
            ]
        ],
        
        'sales_consultation' => [
            'text' => [
                'content' => '💼 欢迎加入销售咨询群！\n\n' .
                           '🎯 我们可以为您提供：\n' .
                           '• 产品价格咨询\n' .
                           '• 定制化方案设计\n' .
                           '• 合作政策解答\n\n' .
                           '📞 急需帮助请直接联系：400-123-4567'
            ],
            'image' => [
                'pic_url' => 'https://cdn.example.com/sales_banner.jpg'
            ]
        ],
        
        'community_discussion' => [
            'text' => [
                'content' => '🌟 欢迎加入用户交流社区！\n\n' .
                           '💬 在这里您可以：\n' .
                           '• 分享使用心得\n' .
                           '• 参与产品讨论\n' .
                           '• 结识志同道合的朋友\n\n' .
                           '🏆 活跃用户将有机会获得专属礼品'
            ],
            'miniprogram' => [
                'title' => '社区积分商城',
                'appid' => 'wx1234567890abcdef',
                'page' => 'pages/community/points',
                'pic_media_id' => 'community_pic_media_id'
            ]
        ]
    ];
    
    $createdTemplates = [];
    
    foreach ($templates as $type => $template) {
        $result = $groupWelcomeTemplate->add($template);
        
        if ($result['errcode'] === 0) {
            $createdTemplates[$type] = $result['template_id'];
            echo "创建 {$type} 欢迎语成功: {$result['template_id']}\n";
        } else {
            echo "创建 {$type} 欢迎语失败: {$result['errmsg']}\n";
        }
        
        sleep(1); // 避免频率限制
    }
    
    return $createdTemplates;
}

$templateIds = createWelcomeTemplatesByType($groupWelcomeTemplate);
```

### 定期更新欢迎语内容

```php
// 根据节日或活动更新欢迎语
function updateSeasonalWelcome($groupWelcomeTemplate, $templateId) {
    $currentMonth = date('n');
    $seasonalContent = '';
    
    switch ($currentMonth) {
        case 12:
        case 1:
        case 2:
            $seasonalContent = '❄️ 冬日暖心服务，温暖每一位客户\n';
            break;
        case 3:
        case 4:
        case 5:
            $seasonalContent = '🌸 春暖花开，与您共享美好时光\n';
            break;
        case 6:
        case 7:
        case 8:
            $seasonalContent = '☀️ 夏日清凉，为您提供贴心服务\n';
            break;
        case 9:
        case 10:
        case 11:
            $seasonalContent = '🍂 秋高气爽，收获满满的服务体验\n';
            break;
    }
    
    $updateData = [
        'text' => [
            'content' => $seasonalContent . 
                        '欢迎加入我们的客户服务群！\n\n' .
                        '我们将为您提供专业的服务支持。'
        ]
    ];
    
    $result = $groupWelcomeTemplate->edit($templateId, $updateData);
    
    if ($result['errcode'] === 0) {
        echo "季节性欢迎语更新成功\n";
    } else {
        echo "更新失败: {$result['errmsg']}\n";
    }
}

// 使用示例
if (!empty($templateIds['product_support'])) {
    updateSeasonalWelcome($groupWelcomeTemplate, $templateIds['product_support']);
}
```

### 欢迎语模板管理

```php
// 获取和管理所有欢迎语模板
function manageWelcomeTemplates($groupWelcomeTemplate, $templateIds) {
    foreach ($templateIds as $type => $templateId) {
        // 获取模板详情
        $detail = $groupWelcomeTemplate->get($templateId);
        
        if ($detail['errcode'] === 0) {
            echo "\n=== {$type} 模板 ({$templateId}) ===\n";
            echo "文本内容: " . (isset($detail['text']['content']) ? 
                substr($detail['text']['content'], 0, 50) . '...' : '无') . "\n";
            echo "是否包含图片: " . (isset($detail['image']) ? '是' : '否') . "\n";
            echo "是否包含链接: " . (isset($detail['link']) ? '是' : '否') . "\n";
            echo "是否包含小程序: " . (isset($detail['miniprogram']) ? '是' : '否') . "\n";
        }
    }
    
    // 清理不再使用的模板
    $unusedTemplates = ['old_template_id_1', 'old_template_id_2'];
    
    foreach ($unusedTemplates as $templateId) {
        $deleteResult = $groupWelcomeTemplate->delete($templateId);
        
        if ($deleteResult['errcode'] === 0) {
            echo "已删除废弃模板: {$templateId}\n";
        }
    }
}

manageWelcomeTemplates($groupWelcomeTemplate, $templateIds);
```

## 注意事项

1. **模板数量限制**：每个企业最多可创建100个入群欢迎语素材
2. **媒体资源**：图片和小程序封面需要先上传获取media_id
3. **内容审核**：欢迎语内容需要符合企业微信规范
4. **权限要求**：需要企业微信管理员权限
5. **更新频率**：避免频繁修改同一模板

## 最佳实践

1. **内容个性化**：根据不同群组类型设计不同的欢迎语
2. **信息丰富**：合理使用文本、图片、链接等多种形式
3. **定期更新**：根据季节、活动等适时更新内容
4. **简洁明了**：避免内容过长，突出重点信息
5. **引导行为**：通过欢迎语引导用户进行下一步操作

## 错误码说明

| 错误码 | 说明 |
|--------|------|
| 0 | 成功 |
| 40003 | 无效的UserID |
| 40013 | 不合法的CorpID |
| 41001 | 缺少access_token参数 |
| 84061 | 不合法的模板ID |
| 84062 | 模板已达到数量上限 |
| 84063 | 模板内容过长 |
| 84064 | 不合法的媒体文件 |