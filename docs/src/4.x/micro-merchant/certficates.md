# 获取平台证书
调用获取平台证书接口之前，请前往微信支付商户平台升级API证书，升级后才可成功调用本接口。

```php
// 获取到证书后可以做缓存处理，无需每次重新获取
$response = $app->certficates->get(bool $returnRaw = false);

// 获取到平台证书后，可以直接使用 setCertificate 方法把证书配置追加到配置项里面去
$app->setCertificate(string $certificate, string $serialNo);
```
> $returnRaw 不填默认为false时，请确保你的PHP已安装了sodium扩展    
> 返回值：固定array格式的解密后的证书信息

> $returnRaw 传入true时     
> 返回值：Response对象`$response->getBody()->getContents();`获取到微信返回xml原始数据
