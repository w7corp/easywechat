# 对账单

## 下载对账单

> 调用参数正确会返回一个 `EasyWeChat\Kernel\Http\StreamResponse` 对象，否则会返回相应错误信息

Example:

```php
$bill = $app->bill->get('20140603'); // type: ALL
// or
$bill = $app->bill->get('20140603', 'SUCCESS'); // type: SUCCESS

// 调用正确，`$bill` 为 csv 格式的内容，保存为文件：
$bill->saveAs('your/path/to', 'file-20140603.csv');
```

第二个参数为账单类型，参考：https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_6 中 `bill_type`，默认为 `ALL`
