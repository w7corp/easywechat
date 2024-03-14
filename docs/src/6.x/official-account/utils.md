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

## 示例
页面生成js配置（1.4和1.6版本任选一即可）：
```html
<script src="https://res.wx.qq.com/open/js/jweixin-1.4.0.js" type="text/javascript" charset="utf-8"></script>
<!-- <script src="https://res.wx.qq.com/open/js/jweixin-1.6.0.js" type="text/javascript" charset="utf-8"></script> -->
<script type="text/javascript" charset="utf-8">
  wx.config(<?php echo json_encode($app->getUtils()->buildJsSdkConfig('当前页面url', ['updateAppMessageShareData', 'updateTimelineShareData'], [], false)); ?>);
</script>
```
结果如下：
```html
<script src="https://res.wx.qq.com/open/js/jweixin-1.4.0.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
  wx.config({
    "jsApiList":["updateAppMessageShareData","updateTimelineShareData"],
    "openTagList":[],
    "debug":false,
    "url":"当前页面url",
    "nonceStr":"mYEeh068LPuWp06u",
    "timestamp":1710381708,
    "appId":"wxcb0f*****f5f6c2",
    "signature":"9147682d4f77f7f03162915446f90288cafbda93"
  });
</script>
```
