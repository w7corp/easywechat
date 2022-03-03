# 数据统计与分析

获取小程序概况趋势：

```php
$app->data_cube->summaryTrend('20170313', '20170313')
```
开始日期与结束日期的格式为 yyyymmdd。

## API

>  - `summaryTrend(string $from, string $to);` 概况趋势
>  - `dailyVisitTrend(string $from, string $to);` 访问日趋势
>  - `weeklyVisitTrend(string $from, string $to);` 访问周趋势
>  - `monthlyVisitTrend(string $from, string $to);` 访问月趋势
>  - `visitDistribution(string $from, string $to);` 访问分布
>  - `dailyRetainInfo(string $from, string $to);` 访问日留存
>  - `weeklyRetainInfo(string $from, string $to);` 访问周留存
>  - `monthlyRetainInfo(string $from, string $to);` 访问月留存
>  - `visitPage(string $from, string $to);` 访问页面
>  - `userPortrait(string $from, string $to);` 用户画像分布数据

