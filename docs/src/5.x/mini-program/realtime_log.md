# 实时日志

小程序实时日志功能允许开发者查询小程序的实时运行日志，帮助定位和解决线上问题。

## 获取实例

```php
$realtimeLog = $app->realtime_log;
```

## 查询实时日志

查询指定时间段内的实时日志：

```php
$result = $realtimeLog->search(
    '20230601',      // 日期，格式：yyyymmdd
    1685577600,      // 开始时间戳（秒）
    1685581200,      // 结束时间戳（秒）
    [
        'module' => 'default',     // 模块名，可选
        'keyword' => 'error',      // 关键词过滤，可选  
        'level' => 'error',        // 日志级别过滤，可选
        'page_size' => 20,         // 每页数量，默认20，最大100
        'page_num' => 1            // 页码，从1开始
    ]
);
```

**参数说明：**
- `date` string 查询日期，格式：yyyymmdd
- `beginTime` int 开始时间戳（秒）
- `endTime` int 结束时间戳（秒）
- `options` array 可选参数
  - `module` string 模块名过滤
  - `keyword` string 关键词过滤
  - `level` string 日志级别过滤（info、warn、error）
  - `page_size` int 每页数量，默认20，最大100
  - `page_num` int 页码，从1开始

**返回结果：**
```json
{
    "errcode": 0,
    "errmsg": "ok",
    "data": [
        {
            "timestamp": 1685577660,
            "level": "error",
            "module": "default",
            "message": "网络请求失败",
            "stack": "Error: request timeout\n  at ...",
            "page": "pages/index/index",
            "function": "onLoad",
            "line": 25
        }
    ],
    "total": 156,
    "page_num": 1,
    "page_size": 20
}
```

## 使用示例

### 查询错误日志

```php
use EasyWeChat\Factory;

$config = [
    'app_id' => 'your-app-id',
    'secret' => 'your-app-secret',
    // ...
];

$app = Factory::miniProgram($config);
$realtimeLog = $app->realtime_log;

// 查询最近1小时的错误日志
$endTime = time();
$beginTime = $endTime - 3600; // 1小时前
$date = date('Ymd', $endTime);

$result = $realtimeLog->search($date, $beginTime, $endTime, [
    'level' => 'error',
    'page_size' => 50
]);

if ($result['errcode'] === 0) {
    echo "总共找到 {$result['total']} 条错误日志\n";
    
    foreach ($result['data'] as $log) {
        echo "时间：" . date('Y-m-d H:i:s', $log['timestamp']) . "\n";
        echo "页面：{$log['page']}\n";
        echo "消息：{$log['message']}\n";
        echo "堆栈：{$log['stack']}\n";
        echo "---\n";
    }
}
```

### 查询特定关键词日志

```php
// 查询包含"支付"关键词的日志
$result = $realtimeLog->search($date, $beginTime, $endTime, [
    'keyword' => '支付',
    'page_size' => 30
]);

if ($result['errcode'] === 0) {
    foreach ($result['data'] as $log) {
        echo "级别：{$log['level']}\n";
        echo "消息：{$log['message']}\n";
        echo "页面：{$log['page']}\n";
        echo "---\n";
    }
}
```

### 分页查询日志

```php
$pageNum = 1;
$pageSize = 20;

do {
    $result = $realtimeLog->search($date, $beginTime, $endTime, [
        'page_num' => $pageNum,
        'page_size' => $pageSize
    ]);
    
    if ($result['errcode'] === 0 && !empty($result['data'])) {
        echo "第 {$pageNum} 页：\n";
        
        foreach ($result['data'] as $log) {
            echo "- {$log['message']}\n";
        }
        
        $pageNum++;
        
        // 检查是否还有更多页
        $hasMore = ($pageNum - 1) * $pageSize < $result['total'];
    } else {
        $hasMore = false;
    }
} while ($hasMore);
```

### 查询特定模块日志

```php
// 查询自定义模块的日志
$result = $realtimeLog->search($date, $beginTime, $endTime, [
    'module' => 'payment',  // 假设你在小程序中定义了payment模块
    'level' => 'warn'
]);

if ($result['errcode'] === 0) {
    echo "支付模块警告日志：\n";
    foreach ($result['data'] as $log) {
        echo "时间：" . date('Y-m-d H:i:s', $log['timestamp']) . "\n";
        echo "函数：{$log['function']}\n";
        echo "行号：{$log['line']}\n";
        echo "消息：{$log['message']}\n\n";
    }
}
```

## 小程序端配置

要使用实时日志功能，需要在小程序端进行相应配置：

### 1. 开启实时日志

在小程序的 `app.js` 中：

```javascript
App({
  onLaunch() {
    // 开启实时日志
    const logger = wx.getRealtimeLogManager();
    
    // 设置日志级别
    logger.setFilterMsg('test');
    
    // 记录日志
    logger.info('应用启动');
  }
});
```

### 2. 记录日志

在需要记录日志的地方：

```javascript
const logger = wx.getRealtimeLogManager();

// 记录信息日志
logger.info('用户操作', { action: 'click', button: 'submit' });

// 记录警告日志
logger.warn('网络慢', { latency: 2000 });

// 记录错误日志
logger.error('请求失败', error);
```

## 注意事项

1. **时间范围限制**：单次查询时间范围不能超过1天
2. **查询频率限制**：API调用有频率限制，请合理控制调用频次
3. **日志保留期**：实时日志通常保留7天
4. **数据量限制**：单次查询最多返回100条日志
5. **权限要求**：需要小程序管理员权限才能查询日志
6. **模块名规范**：模块名建议使用英文，避免特殊字符

## 最佳实践

1. **合理设置查询时间范围**：避免查询过长时间段的日志
2. **使用关键词过滤**：通过关键词快速定位问题日志
3. **分级查询**：先查询error级别，再查询warn和info
4. **结合监控告警**：可以定期查询错误日志，实现简单的监控告警
5. **日志结构化**：在小程序端记录日志时，使用结构化的数据格式