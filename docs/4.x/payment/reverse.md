# 撤销订单

目前只有 **刷卡支付** 有此功能。

> 调用支付接口后请勿立即调用撤销订单API，建议支付后至少15s后再调用撤销订单接口。

## 通过内部订单号撤销订单

```php
$app->reverse->byOutTradeNumber("商户系统内部的订单号（out_trade_no）");
```

## 通过微信订单号撤销订单

```php
$app->reverse->byTransactionId("微信的订单号（transaction_id）");
```
