# API 调用

与以往版本不同的是，SDK 不再内置具体 API 的逻辑，所有的 API 均交由开发者自行调用，以获取用户列表为例：

```php
$api = $app->getClient();
```

## 两种调用方式

当前版本准备了两种调用方式：**原始方式调用** 和 **链式调用**，请根据你的喜好自行选择使用方式，效果一致。

### 方式一：原始方式调用

```php
$response = $api->post('/cgi-bin/user/info/updateremark', ['body' => [
    "openid" => "oDF3iY9ffA-hqb2vVvbr7qxf6A0Q",
    "remark" => "pangzi"
]]);
```

#### 语法说明

```php
Symfony\Contracts\HttpClient\ResponseInterface {get/post/patch/put/delete}($uri, $options = [])
```

**参数说明：**

- `$uri` 为需要请求的 `path`；
- `$options` 为请求参数，可以指定 `query` / `body` / `headers` 等等，具体请参考：[Symfony\Contracts\HttpClient\HttpClientInterface::OPTIONS_DEFAULTS](https://github.com/symfony/symfony/blob/5.3/src/Symfony/Contracts/HttpClient/HttpClientInterface.php)

---

### 方式二：链式调用

你可以将需要调用的 API 以 `/` 分割 + 驼峰写法的形式，写成如下模式：

```php
$users = $api->cgiBin->user->get->get()->toArray();
```

它最终就是转化为：

```
GET /cgi-bin/user/get
```

#### 链式转换规则

- 请求 path 中的 `/` 为分隔符，切割成属性，例如：`/cgi-bin/user/info/updateremark` 则转换成 `->cgiBin->user->info->updateremark`；
- path 对应的请求方法（HTTP Method），即作为请求对象的末尾执行方法，例如: `->cgiBin->user->info->updateremark->post([...])`；
- 有中横线分隔符(`-`)的，可以使用驼峰（camelCase）风格书写，例如: merchant-service 可写成 merchantService；
- 动态参数，例如 `business_code/{business_code}` 可写成 `->businessCode->{'201202828'}`，或按属性风格，直接写值也可以，例如 `businessCode->{'$myCode'}`；

> :heart: 链式调用参考自朋友 `TheNorthMemory` 的插件 [TheNorthMemory/wechatpay-axios-plugin](https://github.com/TheNorthMemory/wechatpay-axios-plugin) 中的创意。

##### 动态参数示例

URL 中有动态参数，可以用 **单引号变量名写法代替**，然后在请求 `$options` 中传递该参数将会完成替换：

```php
$outTradeNo = 'order123456';
$response = $api->pay->transactions->outTradeNo->{'$outTradeNo'}->get([
    'query'=>[
        'mchid' =>  $app->getMerchant()->getMerchantId()
    ],
    'outTradeNo' => $outTradeNo, // <-- 这里将对应替换 URL 中同名的参数 `$out_trade_no`
]);
```

> 注意： 变量部分一定使用单引号。

#### 参数传递

##### GET

你可以在最后的调用方法里传递对应的参数，例如：

```php
$users = $api->cgiBin->user->get->get([
    'query' => [
            'next_openid' => 'OPENID1',
        ]
    ])->toArray();
```

#### POST

```php
$api->cgiBin->user->info->updateremark->post([
    'body' => [
            "openid" => "oDF3iY9ffA-hqb2vVvbr7qxf6A0Q",
            "remark" => "pangzi"
        ]
    ])->toArray();
```

或者指定 json 格式：

```php
$api->cgiBin->user->info->updateremark->post([
    'json' => [
            "openid" => "oDF3iY9ffA-hqb2vVvbr7qxf6A0Q",
            "remark" => "pangzi"
        ]
    ])->toArray();
```

#### 文件上传

你有两种上传文件的方式可以选择：

##### 从指定路径上传

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

##### 从二进制内容上传

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

---

## 处理响应

API Client 基于 [symfony/http-client](https://github.com/symfony/http-client) 实现，你可以通过以下方式对响应值进行访问：

```php
$response = $api->get('/cgi-bin/user/get', ['query' => ['openid' => '...']]);

// 获取状态码
$statusCode = $response->getStatusCode();

// 获取全部响应头
$headers = $response->getHeaders();

// 获取响应原始内容
$content = $response->getContent();

// 获取 json 转换后的数组格式
$content = $response->toArray();

// 将内容转换成 Stream 返回
$content = $response->toStream();

// 获取其他信息，如："response_headers", "redirect_count", "start_time", "redirect_url" 等.
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
$response = $api->post('/cgi-bin/user/info/updateremark', ['body' => [
    "openid" => "oDF3iY9ffA-hqb2vVvbr7qxf6A0Q",
    "remark" => "pangzi"
]])

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
    'users'=> $api->get('/cgi-bin/user/get'),
    'remark' => $api->post('/cgi-bin/user/info/updateremark', ['body' => ...]),
    'message' => $api->post('/cgi-bin/user/message/custom/send', ['body' => ...]),
];

// 访问任意一个 $response 时将执行并发请求：
$responses['users']->toArray();
```

## 更多使用方法

:book: 更多使用请参考：[symfony/http-client](https://github.com/symfony/http-client)
