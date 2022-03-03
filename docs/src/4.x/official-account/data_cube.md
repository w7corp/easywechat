# 数据统计与分析

通过数据接口，开发者可以获取与公众平台官网统计模块类似但更灵活的数据，还可根据需要进行高级处理。

>
> 1. 接口侧的公众号数据的数据库中仅存储了 **2014年12月1日之后**的数据，将查询不到在此之前的日期，即使有查到，也是不可信的脏数据；
> 2. 请开发者在调用接口获取数据后，将数据保存在自身数据库中，即加快下次用户的访问速度，也降低了微信侧接口调用的不必要损耗。
> 3. 额外注意，获取图文群发每日数据接口的结果中，只有**中间页阅读人数+原文页阅读人数+分享转发人数+分享转发次数+收藏次数 >=3** 的结果才会得到统计，过小的阅读量的图文消息无法统计。

## 示例

```php
$userSummary = $app->data_cube->userSummary('2014-12-07', '2014-12-08');

var_dump($userSummary);
//
//[
//    {
//        "ref_date": "2014-12-07",
//        "user_source": 0,
//        "new_user": 0,
//        "cancel_user": 0
//    }
//    //后续还有ref_date在begin_date和end_date之间的数据
// ]

```

## API

    $from   示例： `2014-02-13` 获取数据的起始日期
    $to     示例： `2014-02-18` 获取数据的结束日期，`$to`允许设置的最大值为昨日

    `$from` 和 `$to` 的差值需小于 “最大时间跨度”（比如最大时间跨度为 1 时，`$from` 和 `$to` 的差值只能为 0，才能小于 1 ），否则会报错

+ `array userSummary(string $from, string $to)` 获取用户增减数据, 最大时间跨度：**7**;
+ `array userCumulate(string $from, string $to)` 获取累计用户数据, 最大时间跨度：**7**;
+ `array articleSummary(string $from, string $to)` 获取图文群发每日数据, 最大时间跨度：**1**;
+ `array articleTotal(string $from, string $to)` 获取图文群发总数据, 最大时间跨度：**1**;
+ `array userReadSummary(string $from, string $to)` 获取图文统计数据, 最大时间跨度：**3**;
+ `array userReadHourly(string $from, string $to)` 获取图文统计分时数据, 最大时间跨度：**1**;
+ `array userShareSummary(string $from, string $to)` 获取图文分享转发数据, 最大时间跨度：**7**;
+ `array userShareHourly(string $from, string $to)` 获取图文分享转发分时数据, 最大时间跨度：**1**;
+ `array upstreamMessageSummary(string $from, string $to)` 获取消息发送概况数据, 最大时间跨度：**7**;
+ `array upstreamMessageHourly(string $from, string $to)` 获取消息发送分时数据, 最大时间跨度：**1**;
+ `array upstreamMessageWeekly(string $from, string $to)` 获取消息发送周数据, 最大时间跨度：**30**;
+ `array upstreamMessageMonthly(string $from, string $to)` 获取消息发送月数据, 最大时间跨度：**30**;
+ `array upstreamMessageDistSummary(string $from, string $to)` 获取消息发送分布数据, 最大时间跨度：**15**;
+ `array upstreamMessageDistWeekly(string $from, string $to)` 获取消息发送分布周数据, 最大时间跨度：**30**;
+ `array upstreamMessageDistMonthly(string $from, string $to)` 获取消息发送分布月数据, 最大时间跨度：**30**;
+ `array interfaceSummary(string $from, string $to)` 获取接口分析数据, 最大时间跨度：**30**;
+ `array interfaceSummaryHourly(string $from, string $to)` 获取接口分析分时数据, 最大时间跨度：**1**;
+ `array cardSummary(string $from, string $to, int $condSource = 0)` 获取普通卡券分析分时数据, 最大时间跨度：**1**;
+ `array freeCardSummary(string $from, string $to, int $condSource = 0, string $cardId = '')` 获取免费券分析分时数据, 最大时间跨度：**1**;
+ `array memberCardSummary(string $from, string $to, int $condSource = 0)` 获取会员卡分析分时数据, 最大时间跨度：**1**;
