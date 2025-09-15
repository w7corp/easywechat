# 搜索功能

小程序搜索功能允许开发者向微信提交小程序页面信息，提升小程序在微信搜索中的展现效果。

## 获取实例

```php
$search = $app->search;
```

## 提交页面信息

### 提交页面URL

```php
$pages = [
    [
        'path' => 'pages/product/detail',
        'query' => 'id=123&category=electronics'
    ],
    [
        'path' => 'pages/article/view', 
        'query' => 'article_id=456'
    ],
    [
        'path' => 'pages/user/profile',
        'query' => 'uid=789'
    ]
];

$result = $search->submitPage($pages);
```

### 参数说明

- `path`: 页面路径（不包含参数）
- `query`: 页面参数（URL查询字符串格式）

## 使用示例

```php
// 批量提交商品页面
$products = [
    ['id' => 'prod_001', 'category' => 'electronics'],
    ['id' => 'prod_002', 'category' => 'clothing'],
    ['id' => 'prod_003', 'category' => 'books']
];

$pages = [];
foreach ($products as $product) {
    $pages[] = [
        'path' => 'pages/product/detail',
        'query' => http_build_query([
            'id' => $product['id'],
            'category' => $product['category']
        ])
    ];
}

$result = $search->submitPage($pages);

if ($result['errcode'] === 0) {
    echo "页面提交成功，共提交 " . count($pages) . " 个页面\n";
}
```

## 注意事项

1. 提交频率限制：避免短时间内大量调用接口
2. 页面有效性：确保提交的页面路径真实存在且可访问
3. 参数准确性：确保query参数与实际页面逻辑匹配
4. 数量限制：单次提交页面数量有限制，建议分批提交