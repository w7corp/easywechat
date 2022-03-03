# 工具

提供微信网页开发 JS-SDK 相关方法

## 配置

```php
<?php
use EasyWeChat\OfficialAccount\Application;

$config = [...];

$app = new Application($config);

$utils = $app->getUtils();
```

## 生成 JS-SDK 签名

:book: [官方文档 - JS-SDK说明文档](https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/JS-SDK.html)

```php
$config = $utils->buildJsSdkConfig(
    url: $url, 
    jsApiList: [],
    openTagList: [], 
    debug: false, 
);

// print
[
    "appId" => "wx...",
    "jsApiList" => [],
    "nonceStr" => "string",
    "openTagList" => [],
    "signature" =>  "sign",
    "timestamp" =>  "timestamp"
];

```
