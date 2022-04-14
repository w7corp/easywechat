# 订单

## 统一下单

没错，什么 H5 支付，公众号支付，扫码支付，支付中签约，全部都是用这个接口下单。

> 参数 `appid`, `mch_id`, `nonce_str`, `sign`, `sign_type` 可不用传入

> 服务商模式下, 需使用 `sub_openid`, 并传入`sub_mch_id` 和`sub_appid`

```php
$result = $app->order->unify([
    'body' => '腾讯充值中心-QQ会员充值',
    'out_trade_no' => '20150806125346',
    'total_fee' => 88,
    'spbill_create_ip' => '123.12.12.123', // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
    'notify_url' => 'https://pay.weixin.qq.com/wxpay/pay.action', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
    'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
    'openid' => 'oUpF8uMuAJO_M2pxb1Q9zNjWeS6o',
]);

//如trade_type = APP
//需要进行二次签名
(new \EasyWeChat\Payment\Jssdk\Client($app))->appConfig($result['prepay_id']);

// $result:
//{
//    "return_code": "SUCCESS",
//    "return_msg": "OK",
//    "appid": "wx2421b1c4390ec4sb",
//    "mch_id": "10000100",
//    "nonce_str": "IITRi8Iabbblz1J",
//    "openid": "oUpF8uMuAJO_M2pxb1Q9zNjWeSs6o",
//    "sign": "7921E432F65EB8ED0CE9755F0E86D72F2",
//    "result_code": "SUCCESS",
//    "prepay_id": "wx201411102639507cbf6ffd8b0779950874",
//    "trade_type": "JSAPI"
//}
```

**第二个参数**为是否[支付中签约](https://pay.weixin.qq.com/wiki/doc/api/pap.php?chapter=18_13&index=5)，默认 `false`

> 支付中签约相关参数 `contract_mchid`, `contract_appid`, `request_serial` 可不用传入

```php
$isContract = true;

$result = $app->order->unify([
    'body' => '腾讯充值中心-QQ会员充值',
    'out_trade_no' => '20150806125346',
    'total_fee' => 88,
    'spbill_create_ip' => '123.12.12.123', // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
    'notify_url' => 'https://pay.weixin.qq.com/wxpay/pay.action', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
    'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
    'openid' => 'oUpF8uMuAJO_M2pxb1Q9zNjWeS6o',

    'plan_id' => 123,// 协议模板id
    'contract_code' => 100001256,// 签约协议号
    'contract_display_account' => '腾讯充值中心',// 签约用户的名称
    'contract_notify_url' => 'http://easywechat.org/contract_notify'
], $isContract);

//$result:
//{
//  "return_code": "SUCCESS",
//  "return_msg": "OK",
//  "appid": "wx123456",
//  "mch_id": "10000100",
//  "nonce_str": "CfOcMkDFblzulYvI",
//  "sign": "B53F4AFEE7FA6AD5739581486A5CB9C9",
//  "result_code": "SUCCESS",
//  "prepay_id": "wx08175759731015754a5c13791522969400",
//  "trade_type": "JSAPI",
//  "plan_id": "123",
//  "request_serial": "1565258279",
//  "contract_code": "100001256",
//  "contract_display_account": "腾讯充值中心",
//  "out_trade_no": "201908088195558331565258279",
//  "contract_result_code": "SUCCESS"
//}
```

## 查询订单

该接口提供所有微信支付订单的查询，商户可以通过该接口主动查询订单状态，完成下一步的业务逻辑。

需要调用查询接口的情况：

> - 当商户后台、网络、服务器等出现异常，商户系统最终未接收到支付通知；
> - 调用支付接口后，返回系统错误或未知交易状态情况；
> - 调用被扫支付 API，返回 USERPAYING 的状态；
> - 调用关单或撤销接口 API 之前，需确认支付状态；

### 根据商户订单号查询

```php
$app->order->queryByOutTradeNumber("商户系统内部的订单号（out_trade_no）");
```

### 根据微信订单号查询

```php
$app->order->queryByTransactionId("微信订单号（transaction_id）");
```

## 关闭订单

> 注意：订单生成后不能马上调用关单接口，最短调用时间间隔为 5 分钟。

```php
$app->order->close(商户系统内部的订单号（out_trade_no）);
```
