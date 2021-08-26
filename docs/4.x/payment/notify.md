# 通知

## 支付结果通知

在用户成功支付后，微信服务器会向该 **订单中设置的回调 URL** 发起一个 POST 请求，请求的内容为一个 XML。里面包含了所有的详细信息，具体请参考：[支付结果通知](https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_7)

而对于用户的退款操作，在退款成功之后也会有一个异步回调通知。

本 SDK 内预置了相关方法，以方便开发者处理这些通知，具体用法如下：

只需要在控制器中使用 `handlePaidNotify()` 方法，在其中对自己的业务进行处理并向微信服务器发送一个响应。

```php
$response = $app->handlePaidNotify(function ($message, $fail) {
    // 你的逻辑
    return true;
    // 或者错误消息
    $fail('Order not exists.');
});

$response->send(); // Laravel 里请使用：return $response;
```

这里需要注意的有几个点：

0. 退款结果通知和扫码支付通知的使用方法均类似。
1. `handlePaidNotify` 只接收一个 [`Closure`](http://php.net/manual/zh/class.closure.php) 匿名函数。
2. 该匿名函数接收两个参数，这两个参数分别为：

   > - `$message` 为微信推送过来的通知信息，为一个数组；
   > - `$fail` 为一个函数，触发该函数可向微信服务器返回对应的错误信息，**微信会稍后重试再通知**。

3. 该函数返回值就是告诉微信 **“我是否处理完成”**。如果你触发 `$fail` 函数，那么微信会在稍后再次继续通知你，直到你明确的告诉它：“我已经处理完成了”，**只有**在函数里 `return true;` 才代表处理完成。

4. `handlePaidNotify` 返回值 `$response` 是一个 Response 对象，如果你要直接输出，使用 `$response->send()`, 在一些框架里（如 Laravel）不是输出而是返回：`return $response`。

通常我们的处理逻辑大概是下面这样（**以下只是伪代码**）：

```php
$response = $app->handlePaidNotify(function($message, $fail){
    // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
    $order = 查询订单($message['out_trade_no']);

    if (!$order || $order->paid_at) { // 如果订单不存在 或者 订单已经支付过了
        return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
    }

    ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////

    if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
        // 用户是否支付成功
        if (array_get($message, 'result_code') === 'SUCCESS') {
            $order->paid_at = time(); // 更新支付时间为当前时间
            $order->status = 'paid';

        // 用户支付失败
        } elseif (array_get($message, 'result_code') === 'FAIL') {
            $order->status = 'paid_fail';
        }
    } else {
        return $fail('通信失败，请稍后再通知我');
    }

    $order->save(); // 保存订单

    return true; // 返回处理完成
});

$response->send(); // return $response;
```

> 注意：请把 “支付成功与否” 与 “是否处理完成” 分开，它俩没有必然关系。
> 比如：微信通知你用户支付完成，但是支付失败了(result_code 为 'FAIL')，你应该**更新你的订单为支付失败**，但是要**告诉微信处理完成**。

## 退款结果通知

使用示例：

```php
$response = $app->handleRefundedNotify(function ($message, $reqInfo, $fail) {
    // 其中 $message['req_info'] 获取到的是加密信息
    // $reqInfo 为 message['req_info'] 解密后的信息
    // 你的业务逻辑...
    return true; // 返回 true 告诉微信“我已处理完成”
    // 或返回错误原因 $fail('参数格式校验错误');
});

$response->send();
```

## 扫码支付通知

扫码支付【模式一】：https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=6_4

```php
// 扫码支付通知接收第三个参数 `$alert`，如果触发该函数，会返回“业务错误”到微信服务器，触发 `$fail` 则返回“通信错误”
$response = $app->handleScannedNotify(function ($message, $fail, $alert) use ($app) {
    // 如：$alert('商品已售空');
    // 如业务流程正常，则要调用“统一下单”接口，并返回 prepay_id 字符串，代码如下
    $result = $app->order->unify([
        'trade_type' => 'NATIVE',
        'product_id' => $message['product_id'],
        // ...
    ]);

    return $result['prepay_id'];
});

$response->send();
```
