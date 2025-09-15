# API 调用

该方法将 API 交由开发者自行调用，微信有部分新的接口4.x并未全部兼容支持,可以使用该方案去自行封装接口：

例如URL Link接口

```php
$response = $app->getClient()->postJson('wxa/generate_urllink', [
    'path' => 'pages/index/index',
    'is_expire' => true,
    'expire_type' => 1,
    'expire_interval' => 1
]);
```

## 语法说明

```php
$app->getClient()->get(string $uri, array $options = [])
$app->getClient()->postJson(string $uri, array $data = [], array $options = [])
$app->getClient()->post(string $uri, array $options = [])
$app->getClient()->postXml(string $uri, array $data = [], array $options = [])
```

### GET

```php
$response = $app->getClient()->get('/cgi-bin/user/list', [
    'query' => [
        'next_openid' => 'OPENID1',
    ]
]);
```

### POST JSON

```php
$response = $app->getClient()->postJson('/cgi-bin/user/info/updateremark', [
    "openid" => "oDF3iY9ffA-hqb2vVvbr7qxf6A0Q",
    "remark" => "pangzi"
]);
```



