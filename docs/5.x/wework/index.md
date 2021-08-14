## 企业微信

企业微信的使用与公众号以及其它几个应用的使用方式都是一致的，使用 `\EasyWeChat\Factory::work($config)` 来初始化：

```php
$config = [
    'corp_id' => 'xxxxxxxxxxxxxxxxx',

    'agent_id' => 100020, // 如果有 agend_id 则填写
    'secret'   => 'xxxxxxxxxx',

    // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
    'response_type' => 'array',

    'log' => [
        'level' => 'debug',
        'file' => __DIR__.'/wechat.log',
    ],
];

$app = Factory::work($config);
```

然后你就可以用 `$app` 来调用企业微信的服务了。