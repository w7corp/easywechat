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

有四种发起支付的方式：WeixinJSBridge, JSSDK, 小程序支付, APP

### WeixinJSBridge 调起支付 API

:book: [官方文档 - WeixinJSBridge 调起支付](https://pay.weixin.qq.com/doc/v3/merchant/4012791857)

```php
$appId = '商户申请的公众号对应的 appid，由微信支付生成，可在公众号后台查看';
$signType = 'RSA'; // 默认RSA，v2要传MD5
$config = $utils->buildBridgeConfig($prepayId, $appId, $signType); // 返回数组
```

调用示例

```js
WeixinJSBridge.invoke(
  'getBrandWCPayRequest',
  {
    timeStamp: "<?= $config['timeStamp'] ?>", //注意 timeStamp 的格式
    nonceStr: "<?= $config['nonceStr'] ?>",
    package: "?= $config['package'] ?>",
    signType: "<?= $config['signType'] ?>",
    paySign: "<?= $config['paySign'] ?>" // 支付签名
  },
  function (res) {
    if (res.err_msg == 'get_brand_wcpay_request:ok') {
      // 使用以上方式判断前端返回,微信团队郑重提示：
      // res.err_msg将在用户支付成功后返回
      // ok，但并不保证它绝对可靠。
    }
  }
)
```

### JSSDK 调起支付 API

:book: [官方文档 - wx.chooseWXPay 调起支付](https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/JS-SDK.html#58)

```php
$appId = '商户申请的公众号对应的 appid，由微信支付生成，可在公众号后台查看';
$signType = 'RSA'; // 默认RSA，v2要传MD5
$config = $utils->buildSdkConfig($prepayId, $appId, $signType); // 返回数组
```

调用实例:

```js
wx.chooseWXPay({
  timestamp: "<?= $config['timestamp'] ?>",
  nonceStr: "<?= $config['nonceStr'] ?>",
  package: "<?= $config['package'] ?>",
  signType: "<?= $config['signType'] ?>",
  paySign: "<?= $config['paySign'] ?>",
  success: function (res) {
    // 支付成功后的回调函数
  }
})
```

### 小程序调起支付 API

:book: [官方文档 - 小程序调起支付 API](https://pay.weixin.qq.com/doc/v3/merchant/4012791898)

```php
$appId = '商户申请的小程序对应的appid，由微信支付生成，可在小程序后台查看';
$signType = 'RSA'; // 默认RSA，v2要传MD5
$config = $utils->buildMiniAppConfig($prepayId, $appId, $signType); // 返回数组
```

调用示例：

```js
wx.requestPayment({
  timeStamp: "<?= $config['timeStamp'] ?>",
  nonceStr: "<?= $config['nonceStr'] ?>",
  package: "<?= $config['package'] ?>",
  signType: "<?= $config['signType'] ?>",
  paySign: "<?= $config['paySign'] ?>",
  success: function (res) {
    // 支付成功后的回调函数
  }
})
```

### APP 调起支付 API

:book: [官方文档 - APP 调起支付 API](https://pay.weixin.qq.com/doc/v3/merchant/4013070351)

```php
$appId = '商户申请的公众号对应的appid，由微信支付生成，可在公众号后台查看';
$config = $utils->buildAppConfig($prepayId, $appId); // 返回数组
```

调用示例：[官方文档 - APP 调起支付 API](https://pay.weixin.qq.com/doc/v3/merchant/4013070351)

### 使用微信支付公钥加密敏感字段 <version-tag>6.17.0+</version-tag>

:book: [官方文档 - 如何使用微信支付公钥加密敏感字段](https://pay.weixin.qq.com/doc/v3/merchant/4012153196)

```php
$config = [
   'platform_certs' => [
       // 如果是「平台证书」模式
       //    可简写使用平台证书文件绝对路径
       // '/path/to/wechatpay/cert.pem',

       // 如果是「平台公钥」模式
       //    使用Key/Value结构， key为平台公钥ID，value为平台公钥文件绝对路径
       // "{$pubKeyId}" => '/path/to/wechatpay/pubkey.pem',
   ],
];
//使用微信支付公钥加密敏感字段可传入$serial(即 $pubKeyId)，或不传默认取第一个证书
$encrypted = $utils->encryptWithRsaPublicKey($plaintext, $serial); // 返回加密后数据
```

调用示例：[官方文档 - 如何使用微信支付公钥加密敏感字段](https://pay.weixin.qq.com/doc/v3/merchant/4013053257)

# 二维码生成工具推荐

> :heart: 建议由前端生成二维码

确实需要用 PHP 生成二维码，那么以下这些供参考：

- [endroid/QrCode](https://github.com/endroid/QrCode)
- [Bacon/BaconQrCode](https://github.com/Bacon/BaconQrCode)
- [SimpleSoftwareIO/simple-qrcode](https://github.com/SimpleSoftwareIO/simple-qrcode) Bacon/BaconQrCode 的 Laravel 版本
- [aferrandini/PHPQRCode](https://github.com/aferrandini/PHPQRCode)
