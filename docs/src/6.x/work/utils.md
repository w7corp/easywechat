# 工具<version-tag>6.7.1+</version-tag>

提供企业微信网页开发 JS-SDK 相关方法

## 配置

```php
<?php
use EasyWeChat\Work\Application;

$config = [...];

$app = new Application($config);

$utils = $app->getUtils();
```

## 生成 config 接口配置

:book: [官方文档 - config 接口配置 说明文档](https://open.work.weixin.qq.com/api/doc/90001/90144/90547)

```php
$config = $utils->buildJsSdkConfig(
    string $url,
    array $jsApiList,
    array $openTagList = [],
    bool $debug = false,
    bool $beta = true
);

// print
[
    'jsApiList' => ['api1','api2'],
    'openTagList' => ['openTag1','openTag2'],
    'debug' => false,
    'beta' => true,
    'url' => 'https://www.easywechat.com/',
    'nonceStr' => 'mock-nonce',
    'timestamp' => 1601234567,
    'appId' => 'mock-appid',
    'signature' => '22772d2fb393ab9f7f6a5a54168a566fbf1ab767'
];
```

## 生成 agentConfig 接口配置

:book: [官方文档 - agentConfig 接口配置 说明文档](https://open.work.weixin.qq.com/api/doc/90001/90144/94325)

```php
$config = $utils->buildJsSdkAgentConfig(
    int $agentId,
    string $url,
    array $jsApiList,
    array $openTagList = [],
    bool $debug = false
);

// print
[
    'jsApiList' => ['api1','api2'],
    'openTagList' => ['openTag1','openTag2'],
    'debug' => false,
    'url' => 'https://www.easywechat.com/',
    'nonceStr' => 'mock-nonce',
    'timestamp' => 1601234567,
    'corpid' => 'mock-corpid',
    'agentid' => 100001,
    'signature' => '22772d2fb393ab9f7f6a5a54168a566fbf1ab767'
];
```
