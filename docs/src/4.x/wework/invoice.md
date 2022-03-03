# 电子发票

```php
$config = [
    'corp_id' => 'xxxxxxxxxxxxxxxxx',
    'secret'   => 'xxxxxxxxxx',
    //...
];

$app = Factory::work($config);
```

## 查询电子发票

https://work.weixin.qq.com/api/doc#11631

API:

```php
mixed get(string $cardId, string $encryptCode)
```

example:

```php
$app->invoice->get('CARDID', 'ENCRYPTCODE');
```

## 批量查询电子发票

https://work.weixin.qq.com/api/doc#11974

API:

```php
mixed select(array $invoices)
```

> $invoices: 发票参数列表

example:

```php
$invoices = [
    ["card_id" => "CARDID1", "encrypt_code" => "ENCRYPTCODE1"],
    ["card_id" => "CARDID2", "encrypt_code" => "ENCRYPTCODE2"]
];

$app->invoice->select($invoices);
```

## 更新发票状态

https://work.weixin.qq.com/api/doc#11633

API:

```php
mixed update(string $cardId, string $encryptCode, string $status)
```

> $status: 发报销状态
>
> > - INVOICE_REIMBURSE_INIT：发票初始状态，未锁定；
> > - INVOICE_REIMBURSE_LOCK：发票已锁定，无法重复提交报销;
> > - INVOICE_REIMBURSE_CLOSURE:发票已核销，从用户卡包中移除

## 批量更新发票状态

https://work.weixin.qq.com/api/doc#11633

API:

```php
mixed batchUpdate(array $invoices, string $openid, string $status)
```

example:

```php
$invoices = [
    ["card_id" => "CARDID1", "encrypt_code" => "ENCRYPTCODE1"],
    ["card_id" => "CARDID2", "encrypt_code" => "ENCRYPTCODE2"]
];
$openid = 'oV-gpwSU3xlMXbq0PqqRp1xHu9O4';

$status = 'INVOICE_REIMBURSE_CLOSURE';

$app->invoice->batchUpdate($invoices, $openid, $status)
```
