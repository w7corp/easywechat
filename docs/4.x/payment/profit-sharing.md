# 分账
> 官方文档 https://pay.weixin.qq.com/wiki/doc/api/allocation.php?chapter=27_1&index=1

```php
use EasyWeChat\Factory;
$config = [
	'app_id'     => '***',
	"secret"     => "***",
	'mch_id'     => '***',
	'key'        => '***',
	'cert_path'  => 'cert.pem',
	'key_path'   => 'key.pem',
	'notify_url' => 'http://***.com/notify.php',
];
$payment = Factory::payment($config);
```

### 添加接收方

> 商户发起添加分账接收方请求，后续可通过发起分账请求将结算后的钱分到该分账接收方。

```php
$receiver = [
	"type"          => "PERSONAL_OPENID",
	"account"       => "…………",//PERSONAL_OPENID：个人openid
	"name"          => "张三",//接收方真实姓名
	"relation_type" => "PARTNER"
];
$payment->profit_sharing->addReceiver($receiver);
$receiver = [
	"type"          => "MERCHANT_ID",
	"account"       => "132456798",//MERCHANT_ID：商户ID
	"name"          => "商户全称",//商户全称
	"relation_type" => "PARTNER"
];
$payment->profit_sharing->addReceiver($receiver);
```

### 删除接收方

```php
$payment->profit_sharing->deleteReceiver($receiver);
```

### 单次分账

```php
$transaction_id = "***";
$out_trade_no = "***";
$receivers = [
	[
		"type"        => "PERSONAL_OPENID",
		"account"     => "***",
		"amount"      => 2,
		"description" => "分到个人"
	],
	[
		"type"        => "MERCHANT_ID",
		"account"     => "***",
		"amount"      => 1,
		"description" => "分到商户"
	]
];
$sharing = $payment->profit_sharing->share($transaction_id,$out_trade_no,$receivers);
```

### 多次分账

```php
$payment->profit_sharing->multiShare($transaction_id,$out_trade_no,$receivers);
```

### 多次分账完结

```php
$params = [
	"transaction_id" => "",
	"out_order_no"   => "",
	"description"    => ""
];
$payment->profit_sharing->markOrderAsFinished($params);
```

### 分账查询

```php
$res = $payment->profit_sharing->query($transaction_id,$out_trade_no);
```

> 查询结果

```
Array
(
    [return_code] => SUCCESS
    [result_code] => SUCCESS
    [mch_id] => ***
    [nonce_str] => 38e92cbe2790642f
    [sign] => 8904B6440C58785540950F2911500F55C9A94CAC75790B0721B9AA470E6BF9A8
    [transaction_id] => 4200000589202007249764665257
    [out_order_no] => 202007241544057945
    [order_id] => 30000103702020072402011591464
    [status] => FINISHED
    [receivers] => [{"type":"MERCHANT_ID","account":"***","amount":7,"description":"解冻给分账方","result":"SUCCESS","finish_time":"20200724172033"},{"type":"PERSONAL_OPENID","account":"***","amount":2,"description":"分到个人1","result":"SUCCESS","finish_time":"20200724172033"},{"type":"PERSONAL_OPENID","account":"***-g4","amount":1,"description":"分到郭","result":"SUCCESS","finish_time":"20200724172034"}]
)
```

### 分账退回

```php
$out_trade_no = "***";//退款订单号
$out_return_no = "***";//系统内部退款单号
$return_amount = 1;
$return_account = "***-g4";
$description = "订单取消";
$payment->profit_sharing->returnShare($out_trade_no,$out_return_no,$return_amount,$return_account,$description);
```
