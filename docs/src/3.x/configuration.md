# 配置


在前面我们已经讲过，初始化 SDK 的时候方法就是创建一个 `EasyWeChat\Foundation\Application` 实例：

```php
use EasyWeChat\Foundation\Application;

$options = [
   // ...
];

$app = new Application($options);

/**
* 如果想要在Application实例化完成之后, 修改某一个options的值,
* 比如服务商+子商户支付回调场景, 所有子商户订单支付信息都是通过同一个服务商的$option 配置进来的,
* 当oauth在微信端验证完成之后, 可以通过动态设置merchant_id来区分具体是哪个子商户
*/
$app['config']->set('oauth.callback','wechat/oauthcallback/'. $sub_merchant_id->id);
```

那么配置的具体选项有哪些，下面是一个完整的列表：

```php
<?php

return [
    /**
     * Debug 模式，bool 值：true/false
     *
     * 当值为 false 时，所有的日志都不会记录
     */
    'debug'  => true,

    /**
     * 账号基本信息，请从微信公众平台/开放平台获取
     */
    'app_id'  => 'your-app-id',         // AppID
    'secret'  => 'your-app-secret',     // AppSecret
    'token'   => 'your-token',          // Token
    'aes_key' => '',                    // EncodingAESKey，安全模式与兼容模式下请一定要填写！！！

    /**
     * 日志配置
     *
     * level: 日志级别, 可选为：
     *         debug/info/notice/warning/error/critical/alert/emergency
     * permission：日志文件权限(可选)，默认为null（若为null值,monolog会取0644）
     * file：日志文件位置(绝对路径!!!)，要求可写权限
     */
    'log' => [
        'level'      => 'debug',
        'permission' => 0777,
        'file'       => '/tmp/easywechat.log',
    ],

    /**
     * OAuth 配置
     *
     * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
     * callback：OAuth授权完成后的回调页地址
     */
    'oauth' => [
        'scopes'   => ['snsapi_userinfo'],
        'callback' => '/examples/oauth_callback.php',
    ],

    /**
     * 微信支付
     */
    'payment' => [
        'merchant_id'        => 'your-mch-id',
        'key'                => 'key-for-signature',
        'cert_path'          => 'path/to/your/cert.pem', // XXX: 绝对路径！！！！
        'key_path'           => 'path/to/your/key',      // XXX: 绝对路径！！！！
        // 'device_info'     => '013467007045764',
        // 'sub_app_id'      => '',
        // 'sub_merchant_id' => '',
        // ...
    ],

    /**
     * Guzzle 全局设置
     *
     * 更多请参考： http://docs.guzzlephp.org/en/latest/request-options.html
     */
    'guzzle' => [
        'timeout' => 3.0, // 超时时间（秒）
        //'verify' => false, // 关掉 SSL 认证（强烈不建议！！！）
    ],
];
```

> :heart: 安全模式下请一定要填写 `aes_key`

## 日志文件

配置文件里的`/tmp/...`是绝对路径

如果在 windows 下，去把它改成`C:\foo\bar`的形式，
如果是 Linux ，你已经懂了……

如果需要按日独立存储，可以配置成`'file'  => storage_path('/tmp/easywechat/easywechat_'.date('Ymd').'.log'),`

其它同理……

