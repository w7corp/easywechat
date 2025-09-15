# 实时日志

小程序实时日志功能允许开发者查询小程序的实时运行日志，帮助定位和解决线上问题。

## 获取实例

```php
$realtimeLog = $app->realtime_log;
```

## 查询实时日志

```php
$realtimeLog->search(
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

### 参数说明

- `date`: 查询日期，格式：yyyymmdd
- `beginTime`: 开始时间戳（秒）
- `endTime`: 结束时间戳（秒）
- `options`: 可选参数
  - `module`: 模块名过滤
  - `keyword`: 关键词过滤
  - `level`: 日志级别过滤（info、warn、error）
  - `page_size`: 每页数量，默认20，最大100
  - `page_num`: 页码，从1开始

## 示例用法

```php
// 查询最近1小时的错误日志
$endTime = time();
$beginTime = $endTime - 3600;
$date = date('Ymd', $endTime);

$result = $realtimeLog->search($date, $beginTime, $endTime, [
    'level' => 'error',
    'page_size' => 50
]);

if ($result['errcode'] === 0) {
    foreach ($result['data'] as $log) {
        echo "时间：" . date('Y-m-d H:i:s', $log['timestamp']) . "\n";
        echo "消息：{$log['message']}\n";
    }
}
```