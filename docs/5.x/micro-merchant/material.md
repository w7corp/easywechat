# 商户信息修改
## 修改结算银行卡

```php
$response = $app->material->setSettlementCard([
    // 'sub_mch_id' => '1230000109',
    'account_number' => '银行卡号',
    'bank_name' => '开户银行全称（含支行）',
    'account_bank' => '开户银行',
    'bank_address_code' => '开户银行省市编码',
]);
```
## 修改联系信息

```php
$response = $app->material->updateContact([
    // 'sub_mch_id' => '1230000109',
    'mobile_phone' => '手机号',
    'email' => '邮箱',
    'merchant_name' => '商户简称',
]);
```

> 以上接口调用过 `setSubMchId` 方法则无需传入 `sub_mch_id` 参数