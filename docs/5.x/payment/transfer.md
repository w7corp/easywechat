# 企业付款

> EasyWeChat 4.0.7+

该模块需要用到双向证书，请参考：https://pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=4_3

## 企业付款到用户零钱

```php
$app->transfer->toBalance([
    'partner_trade_no' => '1233455', // 商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
    'openid' => 'oxTWIuGaIt6gTKsQRLau2M0yL16E',
    'check_name' => 'FORCE_CHECK', // NO_CHECK：不校验真实姓名, FORCE_CHECK：强校验真实姓名
    're_user_name' => '王小帅', // 如果 check_name 设置为FORCE_CHECK，则必填用户真实姓名
    'amount' => 10000, // 企业付款金额，单位为分
    'desc' => '理赔', // 企业付款操作说明信息。必填
]);
```

## 查询付款到零钱的订单

```php
$partnerTradeNo = 1233455;
$app->transfer->queryBalanceOrder($partnerTradeNo);
```


## 企业付款到银行卡

企业付款到银行卡需要对银行卡号与姓名进行 RSA 加密，所以这里需要先下载 RSA 公钥到本地（服务器），我们提供了一个命令行工具：[EasyWeChat/console](https://github.com/EasyWeChat/console)，请使用 composer 安装完成。

```bash
$ composer require easywechat/console -vvv
```

然后，在项目根目录执行以下命令下载公钥：

```bash
$ ./vendor/bin/easywechat payment:rsa_public_key \
  >  --mch_id=14339221228 \
  >  --api_key=36YTbDmLgyQ52noqdxgwGiYy \
  >  --cert_path=/Users/overtrue/www/demo/apiclient_cert.pem \
  >  --key_path=/Users/overtrue/www/demo/apiclient_key.pem
```

将会在当前目录生成一个 `./public-14339221228.pem` 文件，你可以将它移动到敏感目录，然后在支付配置文件中加如以下选项：

```php
use EasyWeChat\Factory;

$config = [
    // 必要配置
    'app_id'             => 'xxxx',
    'mch_id'             => 'your-mch-id',
    'key'                => 'key-for-signature',   // API 密钥

    // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
    'cert_path'          => '/path/to/your/cert.pem', // XXX: 绝对路径！！！！
    'key_path'           => '/path/to/your/key',      // XXX: 绝对路径！！！！

    // 将上面得到的公钥存放路径填写在这里
    'rsa_public_key_path' => '/path/to/your/rsa/publick/key/public-14339221228.pem', // <<<------------------------

    'notify_url'         => '默认的订单回调地址',     // 你也可以在下单时单独设置来想覆盖它
];

$app = Factory::payment($config);
```

```php
$result = $app->transfer->toBankCard([
    'partner_trade_no' => '1229222022',
    'enc_bank_no' => '6214830901234564', // 银行卡号
    'enc_true_name' => '安正超',   // 银行卡对应的用户真实姓名
    'bank_code' => '1001', // 银行编号
    'amount' => 100,  // 单位：分
    'desc' => '测试',
]);

```

## 查询付款到银行卡的订单

```php
$partnerTradeNo = 1233455;
$app->transfer->queryBankCardOrder($partnerTradeNo);
```

