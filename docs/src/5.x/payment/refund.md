# 退款

## 申请退款

当交易发生之后一段时间内，由于买家或者卖家的原因需要退款时，卖家可以通过退款接口将支付款退还给买家，微信支付将在收到退款请求并且验证成功之后，按照退款规则将支付款按原路退到买家帐号上。

注意：

> 1、交易时间超过一年的订单无法提交退款；
> 2、微信支付退款支持单笔交易分多次退款，多次退款需要提交原支付订单的商户订单号和设置不同的退款单号。一笔退款失败后重新提交，要采用原来的退款单号。总退款金额不能超过用户实际支付金额。

参考：https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_4

### 根据微信订单号退款

```php
// 参数分别为：微信订单号、商户退款单号、订单金额、退款金额、其他参数
$app->refund->byTransactionId(string $transactionId, string $refundNumber, int $totalFee, int $refundFee, array $config = []);

// Example:
$result = $app->refund->byTransactionId('transaction-id-xxx', 'refund-no-xxx', 10000, 10000, [
    // 可在此处传入其他参数，详细参数见微信支付文档
    'refund_desc' => '商品已售完',
]);

```
### 根据商户订单号退款

```php
// 参数分别为：商户订单号、商户退款单号、订单金额、退款金额、其他参数
$app->refund->byOutTradeNumber(string $number, string $refundNumber, int $totalFee, int $refundFee, array $config = []);

// Example:
$result = $app->refund->byOutTradeNumber('out-trade-no-xxx', 'refund-no-xxx', 20000, 1000, [
    // 可在此处传入其他参数，详细参数见微信支付文档
    'refund_desc' => '退运费',
]);
```

> $refundNumber 为商户退款单号，自己生成用于自己识别即可。

## 查询退款

提交退款申请后，通过调用该接口查询退款状态。退款有一定延时，用零钱支付的退款20分钟内到账，银行卡支付的退款3个工作日后重新查询退款状态。

可通过 4 种不同类型的单号查询：

>  - 微信订单号 => `queryByTransactionId($transactionId)`
>  - 商户订单号 => `queryByOutTradeNumber($outTradeNumber)`
>  - 商户退款单号 => `queryByOutRefundNumber($outRefundNumber)`
>  - 微信退款单号 => `queryByRefundId($refundId)`
