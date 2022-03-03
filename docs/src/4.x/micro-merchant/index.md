# 小微商户

你在阅读本文之前确认你已经仔细阅读了：[微信小微商户专属接口文档](https://pay.weixin.qq.com/wiki/doc/api/xiaowei.php?chapter=19_2)。

PS: ⚠️ 因系统升级，腾讯暂时关闭了小微商户接口，恢复时间未定。调用提交申请接口会提示「PARAM_ERROR」，详细说明可参见[微信开放平台相关帖子](https://developers.weixin.qq.com/community/develop/doc/0000a0ffc9ce28bd4bc9999ba5b800)


## 配置

小微商户整体接口调用方式相对于其他微信接口略有不同，配置时请勿填错，相关配置如下：

```php
use EasyWeChat\Factory;

$config = [
    // 必要配置
    'mch_id'           => 'your-mch-id', // 服务商的商户号
    'key'              => 'key-for-signature', // API 密钥
    'apiv3_key'        => 'APIv3-key-for-signature', // APIv3 密钥
    // API 证书路径(登录商户平台下载 API 证书)
    'cert_path'        => 'path/to/your/cert.pem', // XXX: 绝对路径！！！！
    'key_path'         => 'path/to/your/key', // XXX: 绝对路径！！！！
    // 以下两项配置在获取证书接口时可为空，在调用入驻接口前请先调用获取证书接口获取以下两项配置,如果获取过证书可以直接在这里配置，也可参照本文档获取平台证书章节中示例
    // 'serial_no'     => '获取证书接口获取到的平台证书序列号',
    // 'certificate'   => '获取证书接口获取到的证书内容'
    
    // 以下为可选项
    // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
    'response_type' => 'array'
    'appid'            => 'wx931386123456789e' // 服务商的公众账号 ID
];

$app = Factory::microMerchant($config);

```


`$app` 在所有相关小微商户的文档都是指 `Factory::microMerchant` 得到的实例，就不在每个页面单独写了。

## 使用时值得注意的地方：
1、小微商户所有接口中以下列出参数 `version`, `mch_id`, `nonce_str`, `sign`, `sign_type`, `cert_sn` 可不用传入。

2、所有敏感信息无需手动加密，sdk会在调用接口前自动完成加密

3、在调用入驻等需要敏感信息加密的接口前请先调用获取证书接口然后把配置填入配置项

4、入驻成功获取到子商户号后需帮助子商户调用配置修改等接口可以先调用以下方法，方便调用修改等接口时无需再次传入子商户号
```php
// $subMchId 为子商户号
// $appid    服务商的公众账号 ID
$app->setSubMchId(string $subMchId, string $appId = '');
```
