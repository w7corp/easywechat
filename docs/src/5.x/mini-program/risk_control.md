# 风险控制

小程序风险控制功能提供用户安全等级评估能力，帮助开发者识别和防范风险用户。

## 获取实例

```php
$riskControl = $app->risk_control;
```

## 获取用户安全等级

评估用户的安全风险等级：

```php
$params = [
    'openid' => 'user_openid',           // 用户的openid
    'scene' => 1,                        // 场景值
    'mobile_no' => '13800138000',        // 手机号（可选）
    'client_ip' => '192.168.1.1',       // 客户端IP（可选）
    'email_address' => 'user@test.com',  // 邮箱地址（可选）
    'extended_info' => '{"key":"value"}' // 扩展信息（可选）
];

$result = $riskControl->getUserRiskRank($params);
```

**参数说明：**
- `openid` string 用户的openid，必填
- `scene` int 场景值，必填
  - `1` 注册
  - `2` 营销活动
  - `3` 发布信息
  - `4` 支付行为
  - `5` 其他高风险行为
- `mobile_no` string 用户手机号，可选，有助于提高评估准确性
- `client_ip` string 用户客户端IP，可选
- `email_address` string 用户邮箱，可选
- `extended_info` string 扩展信息，JSON字符串，可选

**返回结果：**
```json
{
    "errcode": 0,
    "errmsg": "ok",
    "risk_rank": 1,
    "unoin_id": "user_union_id"
}
```

**风险等级说明：**
- `0` 风险等级未知
- `1` 风险等级较低
- `2` 风险等级中等
- `3` 风险等级较高
- `4` 风险等级高

## 使用示例

### 注册场景风险评估

```php
use EasyWeChat\Factory;

$config = [
    'app_id' => 'your-app-id',
    'secret' => 'your-app-secret',
    // ...
];

$app = Factory::miniProgram($config);
$riskControl = $app->risk_control;

// 用户注册时进行风险评估
$params = [
    'openid' => 'oABC123DEF456GHI789',
    'scene' => 1, // 注册场景
    'mobile_no' => '13800138000',
    'client_ip' => '192.168.1.100'
];

$result = $riskControl->getUserRiskRank($params);

if ($result['errcode'] === 0) {
    $riskRank = $result['risk_rank'];
    
    switch ($riskRank) {
        case 1:
            echo "用户风险等级较低，允许注册\n";
            // 正常注册流程
            break;
        case 2:
            echo "用户风险等级中等，需要额外验证\n";
            // 要求手机号验证
            break;
        case 3:
        case 4:
            echo "用户风险等级较高，拒绝注册\n";
            // 拒绝注册或要求人工审核
            break;
        default:
            echo "风险等级未知，采用默认策略\n";
            break;
    }
} else {
    echo "风险评估失败：{$result['errmsg']}\n";
    // 采用默认安全策略
}
```

### 营销活动风险评估

```php
// 营销活动参与时进行风险评估
$params = [
    'openid' => 'user_openid',
    'scene' => 2, // 营销活动场景
    'client_ip' => $userIp,
    'extended_info' => json_encode([
        'activity_id' => 'activity_123',
        'prize_type' => 'coupon',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
    ])
];

$result = $riskControl->getUserRiskRank($params);

if ($result['errcode'] === 0 && $result['risk_rank'] <= 2) {
    // 风险等级可接受，允许参与活动
    echo "允许参与营销活动\n";
} else {
    // 风险等级过高，禁止参与
    echo "风险等级过高，禁止参与活动\n";
}
```

### 支付行为风险评估

```php
// 支付前进行风险评估
$params = [
    'openid' => 'user_openid',
    'scene' => 4, // 支付行为场景
    'mobile_no' => $userMobile,
    'client_ip' => $userIp,
    'extended_info' => json_encode([
        'order_amount' => 10000, // 订单金额（分）
        'order_id' => 'order_123456',
        'payment_method' => 'wechat_pay'
    ])
];

$result = $riskControl->getUserRiskRank($params);

if ($result['errcode'] === 0) {
    $riskRank = $result['risk_rank'];
    
    if ($riskRank <= 1) {
        // 低风险，正常支付流程
        echo "支付风险低，进入正常支付流程\n";
    } elseif ($riskRank == 2) {
        // 中等风险，要求额外验证
        echo "支付风险中等，要求短信验证\n";
    } else {
        // 高风险，拒绝支付或人工审核
        echo "支付风险高，需要人工审核\n";
    }
}
```

### 发布信息风险评估

```php
// 用户发布内容时进行风险评估
$params = [
    'openid' => 'user_openid',
    'scene' => 3, // 发布信息场景
    'client_ip' => $userIp,
    'extended_info' => json_encode([
        'content_type' => 'text',
        'content_length' => 500,
        'has_image' => true,
        'publish_frequency' => 5 // 今日发布次数
    ])
];

$result = $riskControl->getUserRiskRank($params);

if ($result['errcode'] === 0) {
    if ($result['risk_rank'] <= 2) {
        echo "允许发布内容\n";
        // 正常发布流程
    } else {
        echo "发布风险较高，内容需要审核\n";
        // 内容进入审核队列
    }
}
```

## 最佳实践

### 1. 合理选择场景值

根据实际业务场景选择合适的场景值，不同场景的风险评估策略可能不同。

### 2. 提供充足的用户信息

提供更多的用户信息（如手机号、IP地址等）有助于提高风险评估的准确性。

### 3. 建立分级处理策略

```php
function handleUserRisk($riskRank, $scene) {
    switch ($scene) {
        case 1: // 注册
            return handleRegistrationRisk($riskRank);
        case 2: // 营销活动
            return handleMarketingRisk($riskRank);
        case 4: // 支付
            return handlePaymentRisk($riskRank);
        default:
            return handleDefaultRisk($riskRank);
    }
}

function handleRegistrationRisk($riskRank) {
    if ($riskRank <= 1) {
        return ['action' => 'allow', 'message' => '允许注册'];
    } elseif ($riskRank == 2) {
        return ['action' => 'verify', 'message' => '需要手机验证'];
    } else {
        return ['action' => 'deny', 'message' => '拒绝注册'];
    }
}
```

### 4. 异常处理

```php
try {
    $result = $riskControl->getUserRiskRank($params);
    
    if ($result['errcode'] === 0) {
        // 处理正常响应
        handleUserRisk($result['risk_rank'], $params['scene']);
    } else {
        // API调用失败，使用默认策略
        logger()->warning('风险评估API调用失败', [
            'errcode' => $result['errcode'],
            'errmsg' => $result['errmsg']
        ]);
        // 采用保守的默认策略
    }
} catch (Exception $e) {
    // 网络异常等，使用默认策略
    logger()->error('风险评估异常', ['exception' => $e->getMessage()]);
    // 采用保守的默认策略
}
```

## 注意事项

1. **API调用频率限制**：请合理控制API调用频率，避免超出限制
2. **用户隐私保护**：确保用户数据的安全传输和存储
3. **结果缓存**：可以对评估结果进行短期缓存，避免重复调用
4. **降级策略**：当API不可用时，应有合理的降级策略
5. **业务适配**：根据具体业务需求调整风险处理策略
6. **监控告警**：建议对高风险用户行为进行监控和告警

## 错误码说明

| 错误码 | 说明 |
|--------|------|
| 0 | 成功 |
| -1 | 系统繁忙，此时请开发者稍候再试 |
| 40003 | touser字段openid为空或者不正确 |
| 45009 | 接口调用超过限额 |
| 47001 | 参数错误 |