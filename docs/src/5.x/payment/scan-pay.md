## 扫码支付

### 模式一：先生成产品二维码，扫码下单后支付

> 请务必先熟悉流程：https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=6_4

#### 生成产品二维码内容

```php
$content = $app->scheme($productId); // $productId 为你的产品/商品ID，用于回调时带回，自己识别即可

//结果示例：weixin://wxpay/bizpayurl?sign=XXXXX&appid=XXXXX&mch_id=XXXXX&product_id=XXXXXX&time_stamp=XXXXXX&nonce_str=XXXXX
```

将 `$content` 生成二维码，SDK 并不内置二维码生成库，使用你熟悉的工具创建二维码即可，比如 PHP 部分有以下工具可以选择：

> - https://github.com/endroid/qr-code
> - https://github.com/SimpleSoftwareIO/simple-qrcode
> - https://github.com/aferrandini/PHPQRCode

#### 处理回调

当用户扫码时，你的回调接口会收到一个通知，调用[统一下单接口](https://www.easywechat.com/5.x/payment/order)创建订单后返回 `prepay_id`，你可以使用下面的代码处理扫码通知：

```php
// 扫码支付通知接收第三个参数 `$alert`，如果触发该函数，会返回“业务错误”到微信服务器，触发 `$fail` 则返回“通信错误”
$response = $app->handleScannedNotify(function ($message, $fail, $alert) use ($app) {
    // 如：$alert('商品已售空');
    // 如业务流程正常，则要调用“统一下单”接口，并返回 prepay_id 字符串，代码如下
    $result = $app->order->unify([
        'trade_type' => 'NATIVE',
        'product_id' => $message['product_id'], // $message['product_id'] 则为生成二维码时的产品 ID
        // ...
    ]);

    return $result['prepay_id'];
});

$response->send();
```

用户在手机上付完钱以后，你会再收到**付款结果通知**，这时候请参考：[处理微信支付通知](https://www.easywechat.com/5.x/payment/notify) 更新您的订单状态。

### 模式二：先下单，生成订单后创建二维码

> ：https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=6_5

#### 根据用户选购的商品生成订单

调用[统一下单接口](https://www.easywechat.com/5.x/payment/order)创建订单：

```php
$result = $app->order->unify([
      'trade_type' => 'NATIVE',
      'product_id' => $message['product_id'], // $message['product_id'] 则为生成二维码时的产品 ID
      // ...
  ]);
```

#### 生成二维码

> 版本 4.1.7+ 支持

从上一步得到的 `$result['code_url']` 得到二维码内容：

将 `$result['code_url']` 生成二维码图片向用户展示即可扫码，生成工具上面自己找一下即可。 SDK 不内置

#### 支付通知

这种方式的通知就只有**付款结果通知**了，这时候请参考：[处理微信支付通知](https://www.easywechat.com/5.x/payment/notify) 更新您的订单状态。
