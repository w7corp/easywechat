# API 调用

与以往版本不同的是，SDK 不再内置具体 API 的逻辑，所有的 API 均交由开发者自行调用，以获取用户列表为例：

```php
$api = $app->getClient();

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

#### 请求参数

##### GET

```php
$users = $api->get('/cgi-bin/user/list'， [
    'query' => [
            'next_openid' => 'OPENID1',
        ]
    ])->toArray();
```

#### POST

```php
$response = $api->post('/cgi-bin/user/info/updateremark', [
    'body' => [
            "openid" => "oDF3iY9ffA-hqb2vVvbr7qxf6A0Q",
            "remark" => "pangzi"
        ]
    ]);
```

或者可以简写为：

```php
$response = $api->post('/cgi-bin/user/info/updateremark', [
        "openid" => "oDF3iY9ffA-hqb2vVvbr7qxf6A0Q",
        "remark" => "pangzi"
    ]);
```

或者指定 json 格式：

```php
$response = $api->post('/cgi-bin/user/info/updateremark', [
    'json' => [
            "openid" => "oDF3iY9ffA-hqb2vVvbr7qxf6A0Q",
            "remark" => "pangzi"
        ]
    ]);
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
// 获取响应原始内容（不抛出异常）
$content = $response->getContent(false);

// 获取 json 转换后的数组格式
$content = $response->toArray();
// 获取 json 转换后的数组格式（不抛出异常）
$content = $response->toArray(false);

// 将内容转换成 Stream 返回
$content = $response->toStream();
// 将内容转换成 Stream 返回 (不抛出异常)
$content = $response->toStream(false);

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
