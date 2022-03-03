# 提现相关

## 查询提现状态

```php
$response = $app->withdraw->queryWithdrawalStatus($date, $subMchId = '');
```
## 重新发起提现

```php
$response = $app->withdraw->requestWithdraw($date, $subMchId = '');
```

> 以上接口调用过 `setSubMchId` 方法则无需传入 `sub_mch_id` 参数