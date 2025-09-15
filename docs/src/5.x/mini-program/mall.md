# 微信小商店

微信小商店是微信官方提供的电商能力，小程序可以通过相关接口管理商品、订单等。

## 获取实例

```php
$mall = $app->mall;
```

## 商品管理

### 导入或更新商品

批量导入或更新商品信息：

```php
$products = [
    [
        'product_id' => 'product_001',
        'title' => '商品标题',
        'sub_title' => '商品副标题',
        'head_imgs' => ['图片URL1', '图片URL2'],
        'category_id' => 1234,
        'brand_id' => 5678,
        'model' => '型号',
        'third_cat_id' => 9012,
        'product_type' => 1,
        'qualification_pics' => ['资质图片URL'],
        'src_wxapp_path' => 'pages/product/detail?id=123',
        'skus' => [
            [
                'sku_id' => 'sku_001',
                'price' => 9900, // 以分为单位
                'original_price' => 12900,
                'status' => 1, // 1:上架 0:下架
                'stock_num' => 100,
                'sku_attrs' => [
                    ['attr_key' => '颜色', 'attr_value' => '红色'],
                    ['attr_key' => '尺寸', 'attr_value' => 'L']
                ]
            ]
        ]
    ]
];

$result = $mall->product->import($products, false); // false表示正式环境
```

### 查询商品信息

查询商品详细信息：

```php
$params = [
    'product_id' => 'product_001',
    'need_edit_spu' => 1
];

$result = $mall->product->query($params);
```

### 获取商品状态

```php
$result = $mall->product->getStatus(['product_001', 'product_002']);
```

### 更新商品状态

```php
$result = $mall->product->updateStatus([
    ['product_id' => 'product_001', 'status' => 1], // 1:上架 0:下架
    ['product_id' => 'product_002', 'status' => 0]
]);
```

## 购物车管理

### 添加商品到购物车

```php
$params = [
    'user_open_id' => 'user_openid',
    'sku_product_id' => 'product_001',
    'sku_id' => 'sku_001',
    'num' => 2
];

$result = $mall->cart->add($params);
```

### 获取购物车商品

```php
$params = [
    'user_open_id' => 'user_openid'
];

$result = $mall->cart->get($params);
```

### 删除购物车商品

```php
$params = [
    'user_open_id' => 'user_openid',
    'sku_product_id' => 'product_001',
    'sku_id' => 'sku_001'
];

$result = $mall->cart->delete($params);
```

## 订单管理

### 生成订单

```php
$orderData = [
    'create_time' => time(),
    'type' => 1,
    'order_id' => 'order_' . time(),
    'openid' => 'user_openid',
    'union_id' => 'user_unionid',
    'product_infos' => [
        [
            'product_id' => 'product_001',
            'sku_id' => 'sku_001',
            'product_cnt' => 2,
            'sale_price' => 9900,
            'head_img' => '商品图片URL',
            'title' => '商品标题',
            'path' => 'pages/product/detail?id=123'
        ]
    ],
    'pay_info' => [
        'pay_method' => '微信支付',
        'pay_method_type' => 1,
        'prepay_id' => 'prepay_id_xxx',
        'prepay_time' => time()
    ],
    'price_info' => [
        'order_price' => 19800,
        'freight' => 1000,
        'discounted_price' => 0,
        'additional_price' => 0,
        'additional_remarks' => ''
    ],
    'delivery_info' => [
        'delivery_type' => 1,
        'receiver_name' => '张三',
        'detailed_address' => '详细地址',
        'tel_number' => '13800138000',
        'country' => '中国',
        'province' => '北京市',
        'city' => '北京市',
        'town' => '朝阳区'
    ]
];

$result = $mall->order->add($orderData);
```

### 更新订单状态

```php
$params = [
    'order_id' => 'order_123',
    'status' => 2, // 订单状态
    'action_type' => 1, // 操作类型
    'action_remark' => '操作备注'
];

$result = $mall->order->updateStatus($params);
```

### 批量获取订单

```php
$params = [
    'start_create_time' => strtotime('-30 days'),
    'end_create_time' => time(),
    'last_index' => '', // 分页标识
    'page_size' => 10
];

$result = $mall->order->list($params);
```

## 媒体文件管理

### 上传图片

```php
$result = $mall->media->uploadImg('/path/to/image.jpg');
```

### 获取图片

```php
$result = $mall->media->getImg('media_id');
```

## 完整示例

```php
use EasyWeChat\Factory;

$config = [
    'app_id' => 'your-app-id',
    'secret' => 'your-app-secret',
    // ...
];

$app = Factory::miniProgram($config);
$mall = $app->mall;

// 导入商品
$products = [
    [
        'product_id' => 'test_product_001',
        'title' => '测试商品',
        'sub_title' => '这是一个测试商品',
        'head_imgs' => ['https://example.com/img1.jpg'],
        'category_id' => 1234,
        'skus' => [
            [
                'sku_id' => 'sku_001',
                'price' => 9900,
                'original_price' => 12900,
                'status' => 1,
                'stock_num' => 100
            ]
        ]
    ]
];

$result = $mall->product->import($products);

if ($result['errcode'] === 0) {
    echo "商品导入成功\n";
    
    // 查询商品信息
    $productInfo = $mall->product->query(['product_id' => 'test_product_001']);
    print_r($productInfo);
}
```

## 注意事项

1. 商品价格以分为单位
2. 图片需要先上传到微信服务器获取media_id或使用HTTPS URL
3. 商品分类ID需要从微信官方获取
4. 订单状态变更需要按照微信规范进行
5. API调用频率有限制，请合理控制调用频次