# 小程序码

## 获取小程序码

### 接口A: 适用于需要的码数量较少的业务场景

API:

```
$app->app_code->get(string $path, array $optional = []);
```

其中 `$optional` 为以下可选参数：

>  - **width** Int - 默认 430 二维码的宽度
>  - **auto_color**  默认 false  自动配置线条颜色，如果颜色依然是黑色，则说明不建议配置主色调
>  - **line_color**  数组，`auto_color` 为 `false` 时生效，使用 rgb 设置颜色 例如 ，示例：`["r" => 0,"g" => 0,"b" => 0]`。

示例代码：

```php
$response = $app->app_code->get('path/to/page');
// 或者
$response = $app->app_code->get('path/to/page', [
    'width' => 600,
    //...
]);

// 或者指定颜色
$response = $app->app_code->get('path/to/page', [
    'width' => 600,
    'line_color' => [
        'r' => 105,
        'g' => 166,
        'b' => 134,
    ],
]);

// $response 成功时为 EasyWeChat\Kernel\Http\StreamResponse 实例，失败时为数组或者你指定的 API 返回格式

// 保存小程序码到文件
if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
    $filename = $response->save('/path/to/directory');
}

// 或
if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
    $filename = $response->saveAs('/path/to/directory', 'appcode.png');
}
```

### 接口B：适用于需要的码数量极多，或仅临时使用的业务场景

API:

```
$app->app_code->getUnlimit(string $scene, array $optional = []);
```

> 其中 $scene 必填，$optinal 与 get 方法一致，多一个 page 参数。

示例代码：

```php
$response = $app->app_code->getUnlimit('scene-value', [
    'page'  => 'path/to/page',
    'width' => 600,
]);
// $response 成功时为 EasyWeChat\Kernel\Http\StreamResponse 实例，失败为数组或你指定的 API 返回类型

// 保存小程序码到文件
if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
    $filename = $response->save('/path/to/directory');
}
// 或
if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
    $filename = $response->saveAs('/path/to/directory', 'appcode.png');
}
```

## 获取小程序二维码

API:

```
$app->app_code->getQrCode(string $path, int $width = null);
```

> 其中 $path 必填，其余参数可留空。

示例代码：

```php
$response = $app->app_code->getQrCode('/path/to/page');

// $response 成功时为 EasyWeChat\Kernel\Http\StreamResponse 实例，失败为数组或你指定的 API 返回类型

// 保存小程序码到文件
if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
    $filename = $response->save('/path/to/directory');
}

// 或
if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
    $filename = $response->saveAs('/path/to/directory', 'appcode.png');
}
```

##
