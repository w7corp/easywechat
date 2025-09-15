# 入群欢迎语

企业微信入群欢迎语功能允许企业管理外部联系人入群时的欢迎消息模板，提升客户体验。

## 获取实例

```php
$groupWelcomeTemplate = $app->group_welcome_template;
```

## 欢迎语模板管理

### 添加入群欢迎语素材

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

### 编辑入群欢迎语素材

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

```php
$result = $groupWelcomeTemplate->get('msgtemplate4doGWjViuUW');
```

### 删除入群欢迎语素材

```php
$result = $groupWelcomeTemplate->delete('msgtemplate4doGWjViuUW');
```

## 参数说明

### 模板支持的内容类型

- `text`: 文本消息内容
- `image`: 图片消息（可选）
- `link`: 链接消息（可选）
- `miniprogram`: 小程序消息（可选）

## 使用示例

```php
// 创建基础文本欢迎语
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
```