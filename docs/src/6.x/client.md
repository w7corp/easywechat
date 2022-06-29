# API 调用

与以往版本不同的是，SDK 不再内置具体 API 的逻辑，所有的 API 均交由开发者自行调用，以更新用户备注为例：

```php
$api = $app->getClient();

$response = $api->post('/cgi-bin/user/info/updateremark', [
    'json' => [
            "openid" => "oDF3iY9ffA-hqb2vVvbr7qxf6A0Q",
            "remark" => "pangzi"
        ]
    ]);

// or
$response = $api->postJson('/cgi-bin/user/info/updateremark', [
    "openid" => "oDF3iY9ffA-hqb2vVvbr7qxf6A0Q",
    "remark" => "pangzi"
]);
```

## 语法说明

```php
get(string $uri, array $options = []): Symfony\Contracts\HttpClient\ResponseInterface
post(string $uri, array $options = []): Symfony\Contracts\HttpClient\ResponseInterface
postJson(string $url, array $options = []): Symfony\Contracts\HttpClient\ResponseInterface
patch(string $uri, array $options = []): Symfony\Contracts\HttpClient\ResponseInterface
put(string $uri, array $options = []): Symfony\Contracts\HttpClient\ResponseInterface
delete(string $uri, array $options = []): Symfony\Contracts\HttpClient\ResponseInterface
```

`$options` 为请求参数，可以指定 `query`/`body`/`json`/`xml`/`headers` 等等，具体请参考：[HttpClientInterface::OPTIONS_DEFAULTS](https://github.com/symfony/symfony/blob/6.1/src/Symfony/Contracts/HttpClient/HttpClientInterface.php)

---

## 请求参数

### GET

```php
$response = $api->get('/cgi-bin/user/list'， [
    'next_openid' => 'OPENID1',
]);
```

### POST

```php
$response = $api->post('/cgi-bin/user/info/updateremark', [
    'body' => \json_encode([
            "openid" => "oDF3iY9ffA-hqb2vVvbr7qxf6A0Q",
            "remark" => "pangzi"
        ])
    ]);
```

或者可以简写为：

```php
$response = $api->postJson('/cgi-bin/user/info/updateremark', [
    "openid" => "oDF3iY9ffA-hqb2vVvbr7qxf6A0Q",
    "remark" => "pangzi"
]);
```

或者指定 xml 格式：

```php
$response = $api->postXml('/mmpaymkttransfers/promotion/transfers', [
    'mch_appid' => $app->getConfig()['app_id'],
    'mchid' => $app->getConfig()['mch_id'],
    'partner_trade_no' => '202203081646729819743',
    'openid' => 'ogn1H45HCRxVRiEMLbLLuABbxxxx',
    'check_name' => 'FORCE_CHECK',
    're_user_name'=> 'overtrue',
    'amount' => 100,
    'desc' => '理赔',
 ]);
```

### 请求证书

你可以在请求支付时指定证书，以微信支付 V2 为例：

```php
$response = $api->post('/mmpaymkttransfers/promotion/transfers', [
    'xml' => [
        'mch_appid' => $app->getConfig()['app_id'],
        'mchid' => $app->getConfig()['mch_id'],
        'partner_trade_no' => '202203081646729819743',
        'openid' => 'ogn1H45HCRxVRiEMLbLLuABbxxxx',
        'check_name' => 'FORCE_CHECK',
        're_user_name'=> 'overtrue',
        'amount' => 100,
        'desc' => '理赔',
    ],
    'local_cert' => $app->getConfig()['cert_path'],
    'local_pk' => $app->getConfig()['key_path'],
    ]);
```

> 参考：[symfony/http-client#options](https://symfony.com/doc/current/reference/configuration/framework.html#local-cert)

### 文件上传

你有两种上传文件的方式可以选择：

#### 从指定路径上传

```php
use EasyWeChat\Kernel\Form\File;
use EasyWeChat\Kernel\Form\Form;

$options = Form::create(
    [
        'media' => File::fromPath('/path/to/image.jpg'),
    ]
)->toArray();

$response = $api->post('cgi-bin/media/upload?type=image', $options);
```

#### 从二进制内容上传

```php
use EasyWeChat\Kernel\Form\File;
use EasyWeChat\Kernel\Form\Form;

$options = Form::create(
    [
        'media' => File::withContents($contents, 'image.jpg'), // 注意：请指定文件名
    ]
)->toArray();

$response = $api->post('cgi-bin/media/upload?type=image', $options);
```

#### 简化写法 <version-tag>6.4.0+</version-tag>

上面的两种传法都可以简写为下面的方式：

```php
// withFile(string $localPath, string $formName = 'file', string $filename = null)
$media = $client->withFile($path, 'media')->post('cgi-bin/media/upload?type=image');

// withFileContents(string $contents, string $formName = 'file', string $filename = null)
$media = $client->withFileContents($contents, 'media', 'filename.png')->post('cgi-bin/media/upload?type=image');
```

## 自定义 access_token

```php
$client->withAccessToken('access_token');
$client->get('xxxx');
$client->post('xxxx');
//...
```

## 预置参数的传递 <version-tag>6.4.0+</version-tag>

在调用 API 的时候难免有的需要传递账号的一些信息，尤其是支付相关的 API，例如[查询订单](https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_2.shtml)：

```php
$client->get('v3/pay/transactions/id/1217752501201407033233368018', [
    'mchid' => $app->getAccount()->getMchid(),
]);
```

不得不把商户号这种基础信息再读取传递一遍，比较麻烦，设计了如下的简化方案：

```php
$client->withMchId()->get('v3/pay/transactions/id/1217752501201407033233368018');
```

原理就是 `with` + `配置 key`：

> 注意: 如果配置key含有下划线的，如 `app_id` 应该转换为大写 `withAppId`

```php
$client->withAppId()->post('/path/to/resources', [...]);
$client->withAppId()->withMchid()->post('/path/to/resources', [...]);
```

也可以自定义值：

```php
$client->withAppId('12345678')->post('/path/to/resources', [...]);
// or
$client->with('appid', '123456')->post('/path/to/resources', [...]);
```

还可以设置别名：把 `appid` 作为参数 `mch_appid` 值使用：

```php
$client->withAppIdAs('mch_appid')->post('/path/to/resources', [...]);
```

其它通用方法：

```php
$client->with('appid')->post(...)
$client->with(['appid', 'mchid'])->post(...)
$client->with(['appid' => '1234565', 'mchid'])->post(...)
```

---

## 处理响应

API Client 基于 [symfony/http-client](https://github.com/symfony/http-client) 实现，你可以通过以下方式对响应值进行访问：

### 异常处理 <version-tag>6.3.0+</version-tag>

当请求失败，例如状态码不为 200 时，默认访问响应内容都会抛出异常：

```php
$response->getContent(); // 这里会抛出异常
```

如果你不希望默认抛出异常，而希望自己处理，可以在配置文件指定 `http.throw` 参数为 `false`：

```php
$config = [
  //...
  'http' => [
    'throw' => false,
    //...
  ],
];
```

这样，你就可以在调用 API 时，自己处理异常：

```php
$options = [
    'query' => [
        'openid' => 'oDF3iY9ffA-hqb2vVvbr7qxf6A0Q',
    ]
];
$response = $api->get('/cgi-bin/user/get', $options);

if ($response->isFailed()) {
    // 出错了，处理异常
}

return $response;
```

或者不改变默认配置的情况下，在调用请求时单独设置`throw(false)`，若该请求失败，也可以自己处理异常。

```php
// $options 同上文，这里省略
$response = $api->get('/cgi-bin/user/get', $options)->throw(false);

if ($response->isFailed()) {
    // 出错了，处理异常
}

return $response;
```

### 数组式访问

EasyWeChat 增强了 API 响应对象，比如增加了数组式访问，你可以不用每次 `toArray` 后再取值，更加便捷美观：

```php
$response = $api->get('/foo/bar');

$response['foo']; // "bar"
isset($response['foo']); // true
```

### 获取状态码

```php
$response->getStatusCode();
// 200
```

### 判断业务是否成功/失败 <version-tag>6.3.0+</version-tag>

比如状态码是 200，但是公众号接口返回 40029 code 错误：

```php
$response->isSuccessful();  // false
$response->isFailed();      // true
```

### 获取响应头

```php
$response->getHeaders();
// ['content-type' => ['application/json;encoding=utf-8'], '...']

$response->getHeader('content-type');
// ['application/json;encoding=utf-8']

$response->getHeaderLine('content-type');
// 'application/json;encoding=utf-8'
```

### 获取响应内容

```php
$response->getContent();
$response->getContent(false); // 失败不抛出异常
// {"foo":"bar"}

// 获取 json 转换后的数组格式
$response->toArray();
$response->toArray(false); // 失败不抛出异常
// ["foo" => "bar"]

// 获取 json
$response->toJson();
$response->toJson(false);
// {"foo":"bar"}

// 将内容转换成流返回
$response->toStream();
$response->toStream(false); // 失败不抛出异常
```

### 转换为 PSR-7 Response <version-tag>6.6.0+</version-tag>

如果你希望直接将 API 响应转换成 [PSR-7 规范](https://www.php-fig.org/psr/psr-7/) Response，可以使用 `toPsrResponse` 方法：

```php
$psrResponse = $response->toPsrResponse();
```

比如在 Laravel 中就可以这样使用：

```php
return $response->toPsrResponse();
```

### 保存到文件 <version-tag>6.3.0+</version-tag>

你可以方便的将内容直接存储到文件：

```php
$path = $response->saveAs('/path/to/file.jpg');
// /path/to/file.jpg
```

### 转换为 Data URLs <version-tag>6.3.0+</version-tag>

你可以将内容转换为[Data URLs](https://developer.mozilla.org/zh-CN/docs/Web/HTTP/Basics_of_HTTP/Data_URIs)

```php
$dataUrl = $response->toDataUrl();
// data:image/png,%89PNG%0D%0A...
```

### 获取其他上下文信息

如："response_headers", "redirect_count", "start_time", "redirect_url" 等：

```php
$httpInfo = $response->getInfo();

// 获取指定信息
$startTime = $response->getInfo('start_time');

// 获取请求日志
$httpLogs = $response->getInfo('debug');
```

:book: 更多使用请参考： [HTTP client: Processing Responses](https://symfony.com/doc/current/http_client.html#processing-responses)

---

## 异步请求

所有的请求都是异步的，当你第一次访问 `$response` 时才会真正的请求，比如：

```php
// 这段代码会立即执行，并不会发起网络请求
$response = $api->postJson('/cgi-bin/user/info/updateremark', [
    "openid" => "oDF3iY9ffA-hqb2vVvbr7qxf6A0Q",
    "remark" => "pangzi"
]);

// 当你尝试访问 $response 的信息时，才会发起请求并等待返回
$contentType = $response->getHeaders()['content-type'][0];

// 尝试获取响应内容将阻塞执行，直到接收到完整的响应内容
$content = $response->getContent();
```

## 并行请求

由于请求天然是异步的，那么你可以很简单实现并行请求：

```php
$responses = [
    $api->get('/cgi-bin/user/get'),
    $api->post('/cgi-bin/user/info/updateremark', ['body' => ...]),
    $api->post('/cgi-bin/user/message/custom/send', ['body' => ...]),
];

// 访问任意一个 $response 时将执行并发请求：
foreach ($responses as $response) {
    $content = $response->getContent();
    // ...
}
```

当然你也可以给每个请求分配名字独立访问：

```php
$responses = [
    'users' => $api->get('/cgi-bin/user/get'),
    'remark' => $api->post('/cgi-bin/user/info/updateremark', ['body' => ...]),
    'message' => $api->post('/cgi-bin/user/message/custom/send', ['body' => ...]),
];

// 访问任意一个 $response 时将执行并发请求：
$responses['users']->toArray();
```

## 失败重试 <version-tag>6.1.0+</version-tag>

默认在公众号、小程序开启了重试机制，你可以通过全局配置或者手动开启重试特性。

> 🚨 不建议在支付模块使用重试功能，因为一旦重试导致支付数据异常，可能造成无法挽回的损失。

### 方式一：全局配置

在支持重试的模块里增加如下配置可以完成重试机制的全局启用：

```php
    'http' => [
        //...
        'retry' => true, // 使用默认配置
        // 'retry' => [
        //     // 仅以下状态码重试
        //     'http_codes' => [429, 500]
        //     'max_retries' => 3
        //     // 请求间隔 (毫秒)
        //     'delay' => 1000,
        //     // 如果设置，每次重试的等待时间都会增加这个系数
        //     // (例如. 首次:1000ms; 第二次: 3 * 1000ms; etc.)
        //     'multiplier' => 0.1
        // ],
    ],
```

### 方式二：手动开启

如果你不想使用基于配置的全局重试机制，你可以使用 `HttpClient::retry()` 方法来开启失败重试能力：

```php
$app->getClient()->retry()->get('/foo/bar');
```

当然，你可以在 `retry` 配置中自定义重试的配置，如下所示：

```php
$app->getClient()->retry([
    'max_retries' => 2,
    //...
])->get('/foo/bar');
```

### 自定义重试策略

如果觉得参数不能满足需求，你还可以自己实现 [`Symfony\Component\HttpClient\RetryStrategyInterface`](https://github.com/symfony/symfony/blob/6.1/src/Symfony/Component/HttpClient/Retry/RetryStrategyInterface.php) 接口来自定义重试策略，然后调用 `retryUsing` 方法来使用它。

> 💡 建议继承基类来拓展，以实现默认重试类的基础功能。

```php
class MyRetryStrategy extends \Symfony\Component\HttpClient\Retry\GenericRetryStrategy
{
    public function shouldRetry(AsyncContext $context, ?string $responseContent, ?TransportExceptionInterface $exception): ?bool
    {
        // 你的自定义逻辑
        // if (...) {
        //     return false;
        // }

        return parent::shouldRetry($context, $responseContent, $exception);
    }
}
```

使用自定义重试策略：

```php
$app->getClient()->retryUsing(new MyRetryStrategy())->get('/foo/bar');
```

## 更多使用方法

:book: 更多使用请参考：[symfony/http-client](https://github.com/symfony/http-client)
