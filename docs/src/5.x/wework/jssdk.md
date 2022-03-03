# JSSDK

企业微信 JSSDK 官方文档：https://open.work.weixin.qq.com/api/doc/90000/90136/90514

## API

### 获取config接口配置

```php
$app->jssdk->buildConfig(array $APIs, $debug = false, $beta = false, $json = true, array $openTagList = []);
```

默认返回 JSON 字符串，当 `$json` 为 `false` 时返回数组，你可以直接使用到网页中。

- 设置当前URL

```php
$app->jssdk->setUrl($url);
$app->jssdk->buildConfig(array $APIs, $debug = false, $beta = false, $json = true, array $openTagList = []);
```
如果不想用默认读取的URL，可以使用此方法手动设置，通常不需要。


- 示例

我们可以生成js配置文件：

```js
<script src="https://res.wx.qq.com/open/js/jweixin-1.4.0.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
    wx.config(<?php echo $app->jssdk->buildConfig(array('updateAppMessageShareData', 'updateTimelineShareData'), true) ?>);
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

### 获取agentConfig接口配置

调用wx.agentConfig之前，必须确保先成功调用wx.config. 注意：从企业微信3.0.24及以后版本（可通过企业微信UA判断版本号），无须先调用wx.config，可直接wx.agentConfig.

```php
$app->jssdk->buildAgentConfig(
        array $jsApiList, // 需要检测的JS接口列表
        $agentId, //应用id
        bool $debug = false,
        bool $beta = false,
        bool $json = true,
        array $openTagList = [],
        string $url = null //设置当前URL
    );
```

- 前端示例

```js
<script src="https://res.wx.qq.com/open/js/jweixin-1.4.0.js" type="text/javascript" charset="utf-8"></script>
<script src="https://open.work.weixin.qq.com/wwopen/js/jwxwork-1.0.0.js"></script>
<script type="text/javascript" charset="utf-8">
wx.config({
    debug: true, // 请在上线前删除它
    appId: 'wx3cf0f39249eb0e60',
    timestamp: 1430009304,
    nonceStr: 'qey94m021ik',
    signature: '4F76593A4245644FAE4E1BC940F6422A0C3EC03E',
    jsApiList: ['updateAppMessageShareData', 'updateTimelineShareData']
});
wx.ready(function(){
    wx.agentConfig({ //调用agentConfig
        corpid: '', 
        agentid: '', 
        timestamp: '', 
        nonceStr: '', 
        signature: '',
        jsApiList: ['selectExternalContact'],
        success: function(res) {
            // 回调
        },
        fail: function(res) {
            if(res.errMsg.indexOf('function not exist') > -1){
                alert('版本过低请升级')
            }
        }
    });
});
wx.error(function(res){
    console.log('失败');  
});
</script>
```

