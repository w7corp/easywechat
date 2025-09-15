# 搜索功能

小程序搜索功能允许开发者向微信提交小程序页面信息，提升小程序在微信搜索中的展现效果。

## 获取实例

```php
$search = $app->search;
```

## 提交页面信息

### 提交页面URL

向微信提交小程序页面URL和参数信息，用于搜索收录：

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

**参数说明：**
- `path` string 页面路径（不包含参数）
- `query` string 页面参数（URL查询字符串格式）

**返回结果：**
```json
{
    "errcode": 0,
    "errmsg": "ok"
}
```

## 使用场景

### 电商小程序商品页面提交

```php
use EasyWeChat\Factory;

$config = [
    'app_id' => 'your-app-id',
    'secret' => 'your-app-secret',
    // ...
];

$app = Factory::miniProgram($config);
$search = $app->search;

// 批量提交商品页面
function submitProductPages($search, $products) {
    $pages = [];
    
    foreach ($products as $product) {
        $pages[] = [
            'path' => 'pages/product/detail',
            'query' => http_build_query([
                'id' => $product['id'],
                'category' => $product['category'],
                'brand' => $product['brand']
            ])
        ];
    }
    
    // 微信建议每次提交不超过1000个页面
    $chunks = array_chunk($pages, 1000);
    
    foreach ($chunks as $chunk) {
        $result = $search->submitPage($chunk);
        
        if ($result['errcode'] === 0) {
            echo "成功提交 " . count($chunk) . " 个商品页面\n";
        } else {
            echo "提交失败: {$result['errmsg']}\n";
        }
        
        // 避免频率限制
        sleep(1);
    }
}

// 示例商品数据
$products = [
    [
        'id' => 'prod_001',
        'category' => 'electronics',
        'brand' => 'apple'
    ],
    [
        'id' => 'prod_002', 
        'category' => 'clothing',
        'brand' => 'nike'
    ],
    [
        'id' => 'prod_003',
        'category' => 'books',
        'brand' => 'penguin'
    ]
];

submitProductPages($search, $products);
```

### 内容平台页面提交

```php
// 提交文章和视频页面
function submitContentPages($search) {
    // 文章页面
    $articlePages = [
        [
            'path' => 'pages/article/detail',
            'query' => 'id=1001&category=technology'
        ],
        [
            'path' => 'pages/article/detail',
            'query' => 'id=1002&category=lifestyle'
        ]
    ];
    
    // 视频页面
    $videoPages = [
        [
            'path' => 'pages/video/player',
            'query' => 'vid=v001&playlist=tech'
        ],
        [
            'path' => 'pages/video/player', 
            'query' => 'vid=v002&playlist=entertainment'
        ]
    ];
    
    // 用户页面
    $userPages = [
        [
            'path' => 'pages/user/profile',
            'query' => 'uid=user001'
        ],
        [
            'path' => 'pages/user/profile',
            'query' => 'uid=user002'
        ]
    ];
    
    // 合并所有页面
    $allPages = array_merge($articlePages, $videoPages, $userPages);
    
    $result = $search->submitPage($allPages);
    
    if ($result['errcode'] === 0) {
        echo "内容页面提交成功，共提交 " . count($allPages) . " 个页面\n";
    } else {
        echo "提交失败: {$result['errmsg']}\n";
    }
}

submitContentPages($search);
```

### 服务类小程序页面提交

```php
// 餐厅预订小程序
function submitRestaurantPages($search) {
    $pages = [
        // 餐厅详情页
        [
            'path' => 'pages/restaurant/detail',
            'query' => 'restaurant_id=rest001&city=beijing'
        ],
        [
            'path' => 'pages/restaurant/detail',
            'query' => 'restaurant_id=rest002&city=shanghai'
        ],
        
        // 菜品页面
        [
            'path' => 'pages/menu/dish',
            'query' => 'dish_id=dish001&restaurant_id=rest001'
        ],
        
        // 预订页面
        [
            'path' => 'pages/booking/form',
            'query' => 'restaurant_id=rest001&date=2023-12-01'
        ],
        
        // 活动页面
        [
            'path' => 'pages/promotion/detail',
            'query' => 'promo_id=promo001&type=discount'
        ]
    ];
    
    $result = $search->submitPage($pages);
    
    if ($result['errcode'] === 0) {
        echo "餐厅页面提交成功\n";
    }
}

submitRestaurantPages($search);
```

### 动态页面提交

```php
// 根据业务数据动态生成页面提交
function submitDynamicPages($search, $database) {
    $pages = [];
    
    // 从数据库获取最新商品
    $latestProducts = $database->getLatestProducts(100);
    foreach ($latestProducts as $product) {
        $pages[] = [
            'path' => 'pages/product/detail',
            'query' => http_build_query([
                'id' => $product['id'],
                'category' => $product['category'],
                'keywords' => $product['keywords'],
                'price_range' => $product['price_range']
            ])
        ];
    }
    
    // 获取热门分类页面
    $popularCategories = $database->getPopularCategories(20);
    foreach ($popularCategories as $category) {
        $pages[] = [
            'path' => 'pages/category/list',
            'query' => http_build_query([
                'category_id' => $category['id'],
                'sort' => 'popular',
                'filter' => json_encode($category['filters'])
            ])
        ];
    }
    
    // 获取活动页面
    $activePromotions = $database->getActivePromotions();
    foreach ($activePromotions as $promo) {
        $pages[] = [
            'path' => 'pages/promotion/detail',
            'query' => http_build_query([
                'promo_id' => $promo['id'],
                'type' => $promo['type'],
                'start_time' => $promo['start_time'],
                'end_time' => $promo['end_time']
            ])
        ];
    }
    
    // 分批提交
    $batches = array_chunk($pages, 500);
    
    foreach ($batches as $index => $batch) {
        $result = $search->submitPage($batch);
        
        if ($result['errcode'] === 0) {
            echo "批次 " . ($index + 1) . " 提交成功，包含 " . count($batch) . " 个页面\n";
        } else {
            echo "批次 " . ($index + 1) . " 提交失败: {$result['errmsg']}\n";
        }
        
        // 控制频率
        sleep(2);
    }
}

// 模拟数据库类
class MockDatabase {
    public function getLatestProducts($limit) {
        // 返回模拟商品数据
        return array_map(function($i) {
            return [
                'id' => "prod_{$i}",
                'category' => ['electronics', 'clothing', 'books'][rand(0, 2)],
                'keywords' => "product,item,buy",
                'price_range' => rand(1, 5) * 100
            ];
        }, range(1, $limit));
    }
    
    public function getPopularCategories($limit) {
        return array_map(function($i) {
            return [
                'id' => "cat_{$i}",
                'filters' => ['brand' => 'all', 'price' => 'any']
            ];
        }, range(1, $limit));
    }
    
    public function getActivePromotions() {
        return [
            [
                'id' => 'promo_001',
                'type' => 'discount',
                'start_time' => strtotime('-1 day'),
                'end_time' => strtotime('+7 days')
            ]
        ];
    }
}

$database = new MockDatabase();
submitDynamicPages($search, $database);
```

### 定期更新页面信息

```php
// 定期提交页面信息的任务
function schedulePageSubmission($search) {
    // 每日提交新增页面
    $dailyNewPages = [
        // 今日新增商品
        [
            'path' => 'pages/product/detail',
            'query' => 'id=new_prod_' . date('Ymd') . '&new=true'
        ],
        
        // 今日文章
        [
            'path' => 'pages/article/detail',
            'query' => 'id=article_' . date('Ymd') . '&date=' . date('Y-m-d')
        ],
        
        // 今日活动
        [
            'path' => 'pages/daily/activity',
            'query' => 'date=' . date('Y-m-d') . '&type=daily'
        ]
    ];
    
    $result = $search->submitPage($dailyNewPages);
    
    if ($result['errcode'] === 0) {
        echo date('Y-m-d H:i:s') . " - 每日页面提交成功\n";
    } else {
        echo date('Y-m-d H:i:s') . " - 提交失败: {$result['errmsg']}\n";
    }
}

// 可以放在定时任务中执行
schedulePageSubmission($search);
```

## 注意事项

1. **提交频率限制**：避免短时间内大量调用接口
2. **页面有效性**：确保提交的页面路径真实存在且可访问
3. **参数准确性**：确保query参数与实际页面逻辑匹配
4. **数量限制**：单次提交页面数量有限制
5. **权限要求**：需要小程序管理员权限

## 最佳实践

1. **合理规划提交**：根据业务重要性确定页面提交优先级
2. **参数优化**：在query中包含有助于搜索的关键参数
3. **定期更新**：定期提交新增和更新的页面信息
4. **监控效果**：关注提交后的搜索展现效果
5. **批量处理**：合理分批提交大量页面

## 搜索优化建议

1. **页面标题优化**：确保页面有清晰的标题
2. **关键词布局**：在参数中包含相关关键词
3. **分类标识**：通过参数明确页面分类
4. **时效性信息**：包含时间相关参数提升时效性
5. **用户体验**：确保搜索进入的页面体验良好

## 错误码说明

| 错误码 | 说明 |
|--------|------|
| 0 | 成功 |
| -1 | 系统繁忙，此时请开发者稍候再试 |
| 40001 | 获取access_token时AppSecret错误 |
| 40013 | 不合法的AppID |
| 41001 | 缺少access_token参数 |
| 45009 | 接口调用超过限额 |
| 47001 | 参数错误 |
| 85064 | pages参数错误 |
| 85065 | 单次提交页面数超过限制 |