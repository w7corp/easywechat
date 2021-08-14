# JSSDK

JSSDK 模块用于生成调起微信支付以及共享收货地址的调用所需的配置参数。

## 配置

```php
use EasyWeChat\Factory;

$config = [
    // 前面的appid什么的也得保留哦
    'app_id'             => 'xxxx',
    'mch_id'             => 'your-mch-id',
    'key'                => 'key-for-signature',
    'cert_path'          => 'path/to/your/cert.pem', // XXX: 绝对路径！！！！
    'key_path'           => 'path/to/your/key',      // XXX: 绝对路径！！！！
    'notify_url'         => '默认的订单回调地址',     // 你也可以在下单时单独设置来想覆盖它
    // 'device_info'     => '013467007045764',
    // 'sub_app_id'      => '',
    // 'sub_merchant_id' => '',
    // ...
];

$payment = Factory::payment($config);

$jssdk = $payment->jssdk;
```

## 生成支付 JS 配置

有三种发起支付的方式：[WeixinJSBridge](https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=7_7&index=6), [JSSDK](https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=15_1), [小程序](https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=7_7)

1. WeixinJSBridge:

    ```php
    $json = $jssdk->bridgeConfig($prepayId); // 返回 json 字符串，如果想返回数组，传第二个参数 false
    ```

    javascript:

    ```js
    ...
    WeixinJSBridge.invoke(
           'getBrandWCPayRequest', <?= $json ?>,
           function(res){
               if(res.err_msg == "get_brand_wcpay_request:ok" ) {
                    // 使用以上方式判断前端返回,微信团队郑重提示：
                    // res.err_msg将在用户支付成功后返回
                    // ok，但并不保证它绝对可靠。
               }
           }
       );
    ...
    ```

2. JSSDK:

    ```php
    $config = $jssdk->sdkConfig($prepayId); // 返回数组
    ```

    javascript:

    ```js
    wx.chooseWXPay({
        timestamp: <?= $config['timestamp'] ?>,
        nonceStr: '<?= $config['nonceStr'] ?>',
        package: '<?= $config['package'] ?>',
        signType: '<?= $config['signType'] ?>',
        paySign: '<?= $config['paySign'] ?>', // 支付签名
        success: function (res) {
            // 支付成功后的回调函数
        }
    });
    ```

3. 小程序:

    ```php
    $config = $jssdk->bridgeConfig($prepayId, false); // 返回数组
    ```

    javascript:

    ```js
    wx.requestPayment({
        timeStamp: <?= $config['timeStamp'] ?>, //注意 timeStamp 的格式
        nonceStr: '<?= $config['nonceStr'] ?>',
        package: '<?= $config['package'] ?>',
        signType: '<?= $config['signType'] ?>',
        paySign: '<?= $config['paySign'] ?>', // 支付签名
        success: function (res) {
            // 支付成功后的回调函数
        }
    });
    ```

## 生成共享收货地址 JS 配置

1. 发起 OAuth 授权，获取用户 `$accessToken`,参考网页授权章节。

2. 使用 `$accessToken` 获取配置

```php
$configForPickAddress = $jssdk->shareAddressConfig($token);

// 拿着这个生成好的配置 $configForPickAddress 去订单页（或者直接显示订单页）写 js 调用了
// ...
```

## 生成 APP 支付配置

```php
$config = $jssdk->appConfig($prepayId);
```

`$config` 为数组格式，你可以用 API 返回给客户端

# 二维码生成工具推荐

你也许需要生成二维码，那么以下这些供参考：

>  - https://github.com/endroid/QrCode
>  - https://github.com/Bacon/BaconQrCode
>  - https://github.com/SimpleSoftwareIO/simple-qrcode (Bacon/BaconQrCode 的 Laravel 版本)
>  - https://github.com/aferrandini/PHPQRCode
