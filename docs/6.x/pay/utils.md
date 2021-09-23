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

有三种发起支付的方式：JSSDK(WeixinJSBridge) 支付, APP 支付, 小程序支付

### JSSDK

:book: [官方文档 - JSAPI 调起支付 API](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_4.shtml)

 ```php
 $appId = '商户申请的公众号对应的 appid，由微信支付生成，可在公众号后台查看';
 $config = $utils->buildSdkConfig($prepayId, $appId); // 返回数组
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

### 小程序调起支付 API

:book: [官方文档 - 小程序调起支付 API](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_5_4.shtml)

 ```php
 $appId = '商户申请的小程序对应的appid，由微信支付生成，可在小程序后台查看';
 $config = $utils->buildMiniAppConfig($prepayId, $appId); // 返回数组
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


### APP 调起支付 API

:book: [官方文档 - APP 调起支付 API](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_2_4.shtml)

 ```php
 $appId = '商户申请的公众号对应的appid，由微信支付生成，可在公众号后台查看';
 $config = $utils->buildAppConfig($prepayId, $appId); // 返回数组
 ```

调用示例：[官方文档 - APP 调起支付 API](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_2_4.shtml)


# 二维码生成工具推荐

你也许需要生成二维码，那么以下这些供参考：

>  - https://github.com/endroid/QrCode
>  - https://github.com/Bacon/BaconQrCode
>  - https://github.com/SimpleSoftwareIO/simple-qrcode (Bacon/BaconQrCode 的 Laravel 版本)
>  - https://github.com/aferrandini/PHPQRCode
