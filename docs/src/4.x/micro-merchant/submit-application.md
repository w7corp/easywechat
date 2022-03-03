# 商户入驻
## 申请入驻

使用申请入驻接口提交你的小微商户资料。

```php
$result = $app->submitApplication([
    'business_code' => '123456', // 业务申请编号
    'id_card_copy'  => 'media_id', // 身份证人像面照片
    // ...
    // 参数太多就不一一列出，自行根据 (小微商户专属文档 -> 申请入驻api) 填写
]);
```

## 查询申请状态

使用申请入驻接口提交小微商户资料后，一般5分钟左右可以通过该查询接口查询具体的申请结果。

```php
$applymentId = '商户申请单号(applyment_id 申请入驻接口返回)';
$businessCode = '业务申请编号(business_code)';
$app->getStatus(string $applymentId, string $businessCode = '');
```
> 商户申请单号和业务申请编号填写一个就行了，当 `applyment_id` 已填写时，`business_code` 字段无效。

当查询申请状态为待签约，接口会一并返回签约二维码，服务商需引导商户使用本人微信扫码完成协议签署。
