# 配置

下面是一个完整的配置样例：

> 不建议你在配置的时候弄这么多，用到啥就配置啥才是最好的，因为大部分用默认值即可。

```php
[
    /**
     * 账号基本信息，请从微信公众平台/开放平台获取
     */
    'app_id'  => 'your-app-id',         // AppID
    'secret'  => 'your-app-secret',     // AppSecret
    'token'   => 'your-token',          // Token
    'aes_key' => '',                    // EncodingAESKey，兼容与安全模式下请一定要填写！！！

    /**
     * 日志配置 (默认不启用日志)
     *
     * level: 日志级别, 可选为：
     *         debug/info/notice/warning/error/critical/alert/emergency
     * path：日志文件位置(绝对路径!!!)，要求可写权限
     */
    // 'logging' => [
    //     'default' => 'dev', // 默认使用的 channel，生产环境可以改为下面的 prod
    //     'channels' => [
    //         // 测试环境
    //         'dev' => [
    //             'driver' => 'single',
    //             'path' => '/tmp/easywechat.log',
    //             'level' => 'debug',
    //         ],
    //         // 生产环境
    //         'prod' => [
    //             'driver' => 'daily',
    //             'path' => '/tmp/easywechat.log',
    //             'level' => 'info',
    //         ],
    //     ],
    // ],

    /**
     * 接口请求相关配置，超时时间等，具体可用参数请参考：
     * https://github.com/symfony/symfony/blob/5.3/src/Symfony/Contracts/HttpClient/HttpClientInterface.php
     */
    'http' => [
        'timeout' => 5.0,
        // 'base_uri' => 'https://api.weixin.qq.com/', // 如果你在国外想要覆盖默认的 url 的时候才使用，根据不同的模块配置不同的 uri
    ],
]
```

> :heart: 安全模式下请一定要填写 `aes_key`