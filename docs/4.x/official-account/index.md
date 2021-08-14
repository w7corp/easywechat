## 公众号

公众号的各模块相对比较统一，用法如下：


```php
use EasyWeChat\Factory;

$config = [
    'app_id' => 'wx3cf0f39249eb0exx',
    'secret' => 'f1c242f4f28f735d4687abb469072axx',

    // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
    'response_type' => 'array',
    
    //...
];

$app = Factory::officialAccount($config);
```

`$app` 在所有相关公众号的文档都是指 `Factory::officialAccount` 得到的实例，就不在每个页面单独写了。
