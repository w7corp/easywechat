# 聊天敏感词


### 新建敏感词规则

```php
$params = [
    'rule_name' => 'rulename',
    'word_list' => [
        '敏感词1', '敏感词2'
    ],
    'semantics_list' => [1, 2, 3],
    'intercept_type' => 1,
    'applicable_range' => [
        'user_list' => ['zhangshan'],
        'department_list' => [2, 3]
    ]
];

$app->product->createInterceptRule($params);
```

### 获取敏感词规则详情

```php
$ruleId = 'ruleId';

$app->product->getInterceptRuleDetails($ruleId);
```

### 删除敏感词规则

```php
$ruleId = 'ruleId';

$app->product->deleteInterceptRule($ruleId);
```


### 编辑敏感词规则

```php
$params = [
    'rule_id' => 'xxxx',
    'rule_name' => 'rulename',
    'word_list' => [
        '敏感词1', '敏感词2'
    ],
    'semantics_list' => [1, 2, 3],
    'intercept_type' => 1,
    'applicable_range' => [
        'user_list' => ['zhangshan'],
        'department_list' => [2, 3]
    ]
];

$app->product->updateInterceptRule($params);
```
