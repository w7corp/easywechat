# 签约

## 公众号签约

> 参数 `appid`, `version`, `timestamp`, `sign` 可不用传入

```php
$result = $app->contract->web([
    'mch_id' => '1200009811',
    'plan_id' => '12535',
    'contract_code' => '100000',
    'contract_display_account' => '微信代扣',
    'notify_url' => 'https://pay.weixin.qq.com/wxpay/pay.action',
]);
```

## APP 签约

```php
$result = $app->contract->app(array $params);
```

## H5 签约

```php
$result = $app->contract->h5(array $params);
```

## 小程序签约

```php
$result = $app->jssdk->contractConfig(array $params);
```

## 申请扣款

```php
$result = $app->contract->apply(array $params);
```

## 申请解约

```php
$result = $app->contract->delete(array $params);
```
