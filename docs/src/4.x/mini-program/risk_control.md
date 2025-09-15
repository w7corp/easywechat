# 风险控制

小程序风险控制功能提供用户安全等级评估能力，帮助开发者识别和防范风险用户。

## 获取实例

```php
$riskControl = $app->risk_control;
```

## 获取用户安全等级

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

### 场景值说明

- `1` 注册
- `2` 营销活动
- `3` 发布信息
- `4` 支付行为
- `5` 其他高风险行为

### 风险等级说明

- `0` 风险等级未知
- `1` 风险等级较低
- `2` 风险等级中等
- `3` 风险等级较高
- `4` 风险等级高

## 示例用法

```php
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
            break;
        case 2:
            echo "用户风险等级中等，需要额外验证\n";
            break;
        case 3:
        case 4:
            echo "用户风险等级较高，拒绝注册\n";
            break;
        default:
            echo "风险等级未知，采用默认策略\n";
            break;
    }
}
```