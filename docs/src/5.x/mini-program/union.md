# 联盟推广

小程序联盟推广功能允许开发者管理推广计划，通过推广获得佣金收益。

## 获取实例

```php
$union = $app->union;
```

## 推广计划管理

### 创建推广计划

创建新的推广计划：

```php
$result = $union->createPromotion('推广计划名称');
```

**参数说明：**
- `promotionSourceName` string 推广计划名称

**返回结果：**
```json
{
    "errcode": 0,
    "errmsg": "ok",
    "promotionSourcePid": "10000123",
    "promotionSourceName": "推广计划名称"
}
```

### 删除推广计划

删除指定的推广计划：

```php
$result = $union->deletePromotion('10000123', '推广计划名称');
```

**参数说明：**
- `promotionSourcePid` string 推广计划ID
- `promotionSourceName` string 推广计划名称

### 更新推广计划

更新推广计划信息：

```php
$result = $union->updatePromotion('10000123', '新的推广计划名称');
```

**参数说明：**
- `promotionSourcePid` string 推广计划ID
- `promotionSourceName` string 新的推广计划名称

### 获取推广计划列表

获取所有推广计划：

```php
$result = $union->getPromotions();
```

**返回结果：**
```json
{
    "errcode": 0,
    "errmsg": "ok",
    "promotionList": [
        {
            "promotionSourcePid": "10000123",
            "promotionSourceName": "推广计划1",
            "createTime": 1635724800,
            "status": 1
        }
    ]
}
```

## 推广商品管理

### 添加推广商品

将商品添加到推广计划：

```php
$result = $union->addProduct('10000123', [
    'productId' => 'product_001',
    'commissionRate' => 1500 // 佣金比例，单位为万分之一，1500表示15%
]);
```

### 移除推广商品

从推广计划中移除商品：

```php
$result = $union->removeProduct('10000123', 'product_001');
```

### 获取推广商品列表

获取推广计划下的所有商品：

```php
$result = $union->getProducts('10000123', $page, $pageSize);
```

**参数说明：**
- `promotionSourcePid` string 推广计划ID
- `page` int 页码，从1开始
- `pageSize` int 每页数量，最大50

## 订单与佣金

### 获取推广订单

查询推广产生的订单：

```php
$result = $union->getOrders([
    'promotionSourcePid' => '10000123',
    'startTime' => strtotime('-30 days'),
    'endTime' => time(),
    'page' => 1,
    'pageSize' => 20
]);
```

**返回结果：**
```json
{
    "errcode": 0,
    "errmsg": "ok",
    "orderList": [
        {
            "orderId": "order_123456",
            "productId": "product_001",
            "productName": "商品名称",
            "orderAmount": 10000,
            "commissionAmount": 1500,
            "orderTime": 1635724800,
            "status": 2,
            "buyerOpenid": "buyer_openid"
        }
    ],
    "totalCount": 156
}
```

### 获取佣金明细

查询佣金收益明细：

```php
$result = $union->getCommissions([
    'promotionSourcePid' => '10000123',
    'startTime' => strtotime('-30 days'),
    'endTime' => time(),
    'page' => 1,
    'pageSize' => 20
]);
```

**返回结果：**
```json
{
    "errcode": 0,
    "errmsg": "ok",
    "commissionList": [
        {
            "orderId": "order_123456",
            "commissionAmount": 1500,
            "commissionTime": 1635724800,
            "status": 1,
            "settleTime": 1635811200
        }
    ],
    "totalAmount": 15000,
    "totalCount": 10
}
```

## 推广数据统计

### 获取推广数据概览

```php
$result = $union->getOverview('10000123', [
    'startTime' => strtotime('-30 days'),
    'endTime' => time()
]);
```

**返回结果：**
```json
{
    "errcode": 0,
    "errmsg": "ok",
    "data": {
        "clickCount": 1520,
        "orderCount": 156,
        "orderAmount": 1560000,
        "commissionAmount": 234000,
        "conversionRate": 10.26
    }
}
```

### 获取推广趋势数据

```php
$result = $union->getTrend('10000123', [
    'startTime' => strtotime('-7 days'),
    'endTime' => time(),
    'granularity' => 'day' // day, hour
]);
```

## 使用示例

### 完整推广流程示例

```php
use EasyWeChat\Factory;

$config = [
    'app_id' => 'your-app-id',
    'secret' => 'your-app-secret',
    // ...
];

$app = Factory::miniProgram($config);
$union = $app->union;

// 1. 创建推广计划
$promotion = $union->createPromotion('春季促销推广');

if ($promotion['errcode'] === 0) {
    $pid = $promotion['promotionSourcePid'];
    echo "推广计划创建成功，ID: {$pid}\n";
    
    // 2. 添加推广商品
    $addProduct = $union->addProduct($pid, [
        'productId' => 'spring_product_001',
        'commissionRate' => 2000 // 20%佣金
    ]);
    
    if ($addProduct['errcode'] === 0) {
        echo "商品添加成功\n";
        
        // 3. 获取推广链接
        $promotionUrl = "https://your-mini-program.com?pid={$pid}&product_id=spring_product_001";
        echo "推广链接: {$promotionUrl}\n";
        
        // 4. 查询推广数据
        sleep(1); // 模拟等待一段时间后查询
        $overview = $union->getOverview($pid, [
            'startTime' => strtotime('-1 day'),
            'endTime' => time()
        ]);
        
        if ($overview['errcode'] === 0) {
            $data = $overview['data'];
            echo "点击数: {$data['clickCount']}\n";
            echo "订单数: {$data['orderCount']}\n";
            echo "订单金额: " . ($data['orderAmount'] / 100) . "元\n";
            echo "佣金收益: " . ($data['commissionAmount'] / 100) . "元\n";
            echo "转化率: {$data['conversionRate']}%\n";
        }
    }
}
```

### 佣金结算示例

```php
// 查询待结算佣金
$commissions = $union->getCommissions([
    'promotionSourcePid' => $pid,
    'status' => 0, // 0:待结算 1:已结算
    'page' => 1,
    'pageSize' => 50
]);

if ($commissions['errcode'] === 0) {
    $totalPendingCommission = 0;
    
    foreach ($commissions['commissionList'] as $commission) {
        $totalPendingCommission += $commission['commissionAmount'];
    }
    
    echo "待结算佣金总额: " . ($totalPendingCommission / 100) . "元\n";
    echo "待结算订单数: " . count($commissions['commissionList']) . "\n";
}
```

### 推广效果分析

```php
// 获取最近7天的推广趋势
$trend = $union->getTrend($pid, [
    'startTime' => strtotime('-7 days'),
    'endTime' => time(),
    'granularity' => 'day'
]);

if ($trend['errcode'] === 0) {
    echo "最近7天推广趋势:\n";
    foreach ($trend['data'] as $dayData) {
        $date = date('Y-m-d', $dayData['timestamp']);
        echo "{$date}: 点击{$dayData['clickCount']}次, 订单{$dayData['orderCount']}个\n";
    }
}

// 计算推广ROI
$overview = $union->getOverview($pid, [
    'startTime' => strtotime('-30 days'),
    'endTime' => time()
]);

if ($overview['errcode'] === 0) {
    $data = $overview['data'];
    $roi = $data['orderAmount'] > 0 ? ($data['commissionAmount'] / $data['orderAmount']) * 100 : 0;
    echo "推广ROI: {$roi}%\n";
}
```

## 注意事项

1. **推广计划限制**：每个小程序的推广计划数量有限制
2. **佣金结算周期**：佣金通常有一定的结算周期，不是实时到账
3. **商品资质要求**：推广的商品需要符合平台规范
4. **数据统计延迟**：推广数据可能有一定延迟
5. **API调用限制**：注意API调用频率限制

## 最佳实践

1. **合理设置佣金比例**：根据商品利润合理设置佣金，既要有吸引力又要保证盈利
2. **数据监控**：定期监控推广数据，及时调整推广策略
3. **推广渠道多样化**：通过多个推广计划覆盖不同的推广渠道
4. **效果追踪**：建立完善的推广效果追踪机制
5. **合规经营**：确保推广活动符合相关法律法规

## 错误码说明

| 错误码 | 说明 |
|--------|------|
| 0 | 成功 |
| -1 | 系统繁忙 |
| 40001 | 获取access_token时AppSecret错误 |
| 40013 | 不合法的AppID |
| 41001 | 缺少access_token参数 |
| 45009 | 接口调用超过限额 |
| 48001 | api功能未授权 |