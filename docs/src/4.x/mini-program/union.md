# 联盟推广

小程序联盟推广功能允许开发者管理推广计划，通过推广获得佣金收益。

## 获取实例

```php
$union = $app->union;
```

## 推广计划管理

### 创建推广计划

```php
$union->createPromotion('推广计划名称');
```

### 删除推广计划

```php
$union->deletePromotion('10000123', '推广计划名称');
```

### 更新推广计划

```php
$union->updatePromotion('10000123', '新的推广计划名称');
```

### 获取推广计划列表

```php
$union->getPromotions();
```

## 推广商品管理

### 添加推广商品

```php
$union->addProduct('10000123', [
    'productId' => 'product_001',
    'commissionRate' => 1500 // 佣金比例，单位为万分之一，1500表示15%
]);
```

### 移除推广商品

```php
$union->removeProduct('10000123', 'product_001');
```

### 获取推广商品列表

```php
$union->getProducts('10000123', $page, $pageSize);
```

## 订单与佣金

### 获取推广订单

```php
$union->getOrders([
    'promotionSourcePid' => '10000123',
    'startTime' => strtotime('-30 days'),
    'endTime' => time(),
    'page' => 1,
    'pageSize' => 20
]);
```

### 获取佣金明细

```php
$union->getCommissions([
    'promotionSourcePid' => '10000123',
    'startTime' => strtotime('-30 days'),
    'endTime' => time(),
    'page' => 1,
    'pageSize' => 20
]);
```

## 推广数据统计

### 获取推广数据概览

```php
$union->getOverview('10000123', [
    'startTime' => strtotime('-30 days'),
    'endTime' => time()
]);
```

### 获取推广趋势数据

```php
$union->getTrend('10000123', [
    'startTime' => strtotime('-7 days'),
    'endTime' => time(),
    'granularity' => 'day' // day, hour
]);
```