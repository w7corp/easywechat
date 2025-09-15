# 微信小商店

微信小商店是微信官方提供的电商能力，小程序可以通过相关接口管理商品、订单等。

## 获取实例

```php
$mall = $app->mall;
```

## 商品管理

### 导入或更新商品

```php
$products = [
    [
        'product_id' => 'product_001',
        'title' => '商品标题',
        'head_imgs' => ['图片URL1', '图片URL2'],
        'category_id' => 1234,
        'skus' => [
            [
                'sku_id' => 'sku_001',
                'price' => 9900, // 以分为单位
                'status' => 1,
                'stock_num' => 100
            ]
        ]
    ]
];

$mall->product->import($products);
```

### 查询商品信息

```php
$mall->product->query(['product_id' => 'product_001']);
```

### 更新商品状态

```php
$mall->product->updateStatus([
    ['product_id' => 'product_001', 'status' => 1]
]);
```

## 购物车管理

### 添加商品到购物车

```php
$mall->cart->add([
    'user_open_id' => 'user_openid',
    'sku_product_id' => 'product_001',
    'sku_id' => 'sku_001',
    'num' => 2
]);
```

### 获取购物车商品

```php
$mall->cart->get(['user_open_id' => 'user_openid']);
```

## 订单管理

### 生成订单

```php
$orderData = [
    'order_id' => 'order_' . time(),
    'openid' => 'user_openid',
    'product_infos' => [
        [
            'product_id' => 'product_001',
            'sku_id' => 'sku_001',
            'product_cnt' => 2,
            'sale_price' => 9900
        ]
    ]
];

$mall->order->add($orderData);
```

### 批量获取订单

```php
$mall->order->list([
    'start_create_time' => strtotime('-30 days'),
    'end_create_time' => time(),
    'page_size' => 10
]);
```

## 媒体文件管理

```php
// 上传图片
$mall->media->uploadImg('/path/to/image.jpg');

// 获取图片
$mall->media->getImg('media_id');
```