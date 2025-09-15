# JSSDK

微信 JSSDK 官方文档：https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141115

## API

#### 获取JSSDK的配置数组

```php
$config = $app->getUtils()->buildJsSdkConfig($url, $jsApiList, $openTagList, $debug);
```

参数说明：
- `$url`: 当前页面的完整URL
- `$jsApiList`: 需要使用的JS接口列表
- `$openTagList`: 需要使用的开放标签列表（可选）
- `$debug`: 是否开启调试模式（可选，默认false）

返回配置数组，包含 appId、timestamp、nonceStr、signature 等字段。

#### 示例

我们可以生成js配置：

```php
$url = 'https://example.com/current-page';
$jsApiList = ['updateAppMessageShareData', 'updateTimelineShareData'];
$config = $app->getUtils()->buildJsSdkConfig($url, $jsApiList, [], true);
```

```js
<script src="https://res.wx.qq.com/open/js/jweixin-1.4.0.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
    wx.config(<?php echo json_encode($config) ?>);
</script>
```

结果如下：

```js
<script src="https://res.wx.qq.com/open/js/jweixin-1.4.0.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
wx.config({
    debug: true, // 请在上线前删除它
    appId: 'wx3cf0f39249eb0e60',
    timestamp: 1430009304,
    nonceStr: 'qey94m021ik',
    signature: '4F76593A4245644FAE4E1BC940F6422A0C3EC03E',
    jsApiList: ['updateAppMessageShareData', 'updateTimelineShareData']
});
</script>
```

