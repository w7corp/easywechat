# 工具

提供各种支付需要的配置生成方法。

## 配置

```php
<?php
use EasyWeChat\Pay\Application;

$config = [...];

$app = new Application($config);

$utils = $app->getUtils();
```

> 注意

## 生成支付 JS 配置

有三种发起支付的方式：WeixinJSBridge, JSSDK, 小程序

### WeixinJSBridge

:book: [官方文档 - WeixinJSBridge](https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=7_7&index=6)

 ```php
 $appId = '商户申请的公众号对应的appid，由微信支付生成，可在公众号后台查看';
 $config = $utils->buildBridgeConfig($prepayId, $appId); // 返回 json 字符串，如果想返回数组，传第二个参数 false
 ```

调用示例

 ```js
 ...
 WeixinJSBridge.invoke(
        'getBrandWCPayRequest', {
              timeStamp: <?= $config['timeStamp'] ?>, //注意 timeStamp 的格式
              nonceStr: '<?= $config['nonceStr'] ?>',
              package: '<?= $config['package'] ?>',
              signType: '<?= $config['signType'] ?>',
              paySign: '<?= $config['paySign'] ?>', // 支付签名
         },
        function (res) {
            if (res.err_msg == "get_brand_wcpay_request:ok" ) {
                 // 使用以上方式判断前端返回,微信团队郑重提示：
                 // res.err_msg将在用户支付成功后返回
                 // ok，但并不保证它绝对可靠。
            }
        }
    );
 ...
 ```

### JSSDK

:book: [官方文档 - JSSDK](https://pay.weixin.qq.com/wiki/doc/apiv3_partner/apis/chapter4_1_4.shtml)

 ```php
 $appId = '商户申请的公众号对应的appid，由微信支付生成，可在公众号后台查看';
 $config = $utils->buildSdkConfig($prepayId, $appId); // 返回数组
 ```

调用示例：

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

### 小程序

:book: [官方文档 - 小程序支付](https://developers.weixin.qq.com/miniprogram/dev/api/payment/wx.requestPayment.html)

 ```php
 $appId = '商户申请的小程序对应的appid，由微信支付生成，可在小程序后台查看';
 $config = $utils->buildMiniAppConfig($prepayId, , $appId); 
 ```

调用示例：

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

## 生成 APP 支付配置

```php
$appId = '微信开放平台审核通过的移动应用appid';
$config = $utils->buildAppConfig($prepayId, $appId);
```

`$config` 为数组格式，你可以用 API 返回给客户端

# 二维码生成工具推荐

你也许需要生成二维码，那么以下这些供参考：

>  - https://github.com/endroid/QrCode
>  - https://github.com/Bacon/BaconQrCode
>  - https://github.com/SimpleSoftwareIO/simple-qrcode (Bacon/BaconQrCode 的 Laravel 版本)
>  - https://github.com/aferrandini/PHPQRCode