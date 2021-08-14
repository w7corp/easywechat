title: 小程序
---

## 实例化

```php
<?php
use EasyWeChat\Foundation\Application;

$options = [
    // ...
    'mini_program' => [
        'app_id'   => 'component-app-id',
        'secret'   => 'component-app-secret',
        'token'    => 'component-token',
        'aes_key'  => 'component-aes-key'
        ],
    // ...
    ];

$app = new Application($options);
$miniProgram = $app->mini_program;
```

## 登录

### 通过 Code 换取 SessionKey

```php
// 3.2 版本
$miniProgram->user->getSessionKey($code);
// 3.3 版本
$miniProgram->sns->getSessionKey($code);
```

## 加密数据解密

```php
$miniProgram->encryptor->decryptData($sessionKey, $iv, $encryptData);
```

## 数据分析

### API

- `summaryTrend($from, $to)` 概况趋势，限定查询1天数据，即 `$from` 要与 `$to` 相同；
- `dailyVisitTrend($from, $to)` 访问日趋势，限定查询1天数据，即 `$from` 要与 `$to` 相同；
- `weeklyVisitTrend($from, $to)` 访问周趋势， `$from` 为周一日期， `$to` 为周日日期；
- `monthlyVisitTrend($from, $to)` 访问月趋势， `$from` 为月初日期， `$to` 为月末日期；
- `visitDistribution($from, $to)` 访问分布，限定查询1天数据，即 `$from` 要与 `$to` 相同；
- `dailyRetainInfo($from, $to)` 访问日留存，限定查询1天数据，即 `$from` 要与 `$to` 相同；
- `weeklyRetainInfo($from, $to)` 访问周留存， `$from` 为周一日期， `$to` 为周日日期；
- `montylyRetainInfo($from, $to)` 访问月留存， `$from` 为月初日期， `$to` 为月末日期；
- `visitPage($from, $to)` 访问页面，限定查询1天数据，即 `$from` 要与 `$to` 相同；

### 代码示例

```php
$miniProgram->stats->summaryTrend('20170313', '20170313');
```
