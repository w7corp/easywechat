# 小程序

## 登录获取用户信息

> 注意：需要关联小程序，并且使用关联后的小程序AgentId与Secret。

```php
$config = [
    'corp_id' => 'xxxxxxxxxxxxxxxxx', //企业id

    'agent_id' => 100020, // 企业微信关联后的AgentId
    'secret'   => 'xxxxxxxxxx', //企业微信关联后的Secret
];

$app = Factory::work($config);

$miniProgram = $app->miniProgram();

$res = $miniProgram->auth->session("js-code");
```

