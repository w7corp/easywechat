# 模板消息

模板消息仅用于公众号向用户发送重要的服务通知，只能用于符合其要求的服务场景中，如信用卡刷卡通知，商品购买成功通知等。不支持广告等营销类消息以及其它所有可能对用户造成骚扰的消息。

## 获取实例

```php
<?php
use EasyWeChat\Foundation\Application;
// ...
$app = new Application($options);

$notice = $app->notice;
```

### API

+ `boolean setIndustry($industryId1, $industryId2)` 修改账号所属行业；
+ `array getIndustry()` 返回所有支持的行业列表，用于做下拉选择行业可视化更新；
+ `string  addTemplate($shortId)` 添加模板并获取模板ID；
+ `collection send($message)` 发送模板消息, 返回消息ID；
+ `array  getPrivateTemplates()` 获取所有模板列表；
+ `array  deletePrivateTemplate($templateId)` 删除指定ID的模板。

非链接调用方法：

```php
$messageId = $notice->send([
        'touser' => 'user-openid',
        'template_id' => 'template-id',
        'url' => 'xxxxx',
        'data' => [
            //...
        ],
    ]);
```

链式调用方法:

    设置模板ID：template / templateId / uses
    设置接收者openId: to / receiver
    设置详情链接：url / link / linkTo
    设置模板数据：data / with

    以上方法都支持 `withXXX` 与 `andXXX` 形式链式调用

```php
$messageId = $notice->to($userOpenId)->uses($templateId)->andUrl($url)->data($data)->send();
// 或者
$messageId = $notice->to($userOpenId)->url($url)->template($templateId)->andData($data)->send();
// 或者
$messageId = $notice->withTo($userOpenId)->withUrl($url)->withTemplate($templateId)->withData($data)->send();
// 或者
$messageId = $notice->to($userOpenId)->url($url)->withTemplateId($templateId)->send();
// ... ...
```

## 示例:

### 模板

```
{{ first.DATA }}

商品明细：

名称：{{ name.DATA }}
价格：{{ price.DATA }}

{{ remark.DATA }}
```

发送模板消息：

```php
$userId = 'OPENID';
$templateId = 'ngqIpbwh8bUfcSsECmogfXcV14J0tQlEpBO27izEYtY';
$url = 'http://overtrue.me';
$data = array(
         "first"  => "恭喜你购买成功！",
         "name"   => "巧克力",
         "price"  => "39.8元",
         "remark" => "欢迎再次购买！",
        );

$result = $notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($userId)->send();
var_dump($result);

// {
//      "errcode":0,
//      "errmsg":"ok",
//      "msgid":200228332
//  }
```

结果：

![notice-demo](http://7u2jwa.com1.z0.glb.clouddn.com/QQ20160111-0@2x.png)

## 模板数据

为了方便大家开发，我们拓展支持以下格式的模板数据，其它格式的数据可能会导致接口调用失败：

- 所有数据项颜色一样的（这是方便的一种方式）:

    ```php
    $data = array(
        "first"    => "恭喜你购买成功！",
        "keynote1" => "巧克力",
        "keynote2" => "39.8元",
        "keynote3" => "2014年9月16日",
        "remark"   => "欢迎再次购买！",
    );
    ```
  默认颜色为'#173177', 你可以通过 `defaultColor($color)` 来修改

- 独立设置每个模板项颜色的：

    + 简便型：

        ```php
        $data = array(
            "first"    => array("恭喜你购买成功！", '#555555'),
            "keynote1" => array("巧克力", "#336699"),
            "keynote2" => array("39.8元", "#FF0000"),
            "keynote3" => array("2014年9月16日", "#888888"),
            "remark"   => array("欢迎再次购买！", "#5599FF"),
        );
        ```
    + 复杂型（也是微信官方唯一支持的方式，估计没有人想这么用）：

        ```php
        $data = array(
            "first"    => array("value" => "恭喜你购买成功！", "color" => '#555555'),
            "keynote1" => array("value" => "巧克力", "color" => "#336699"),
            "keynote2" => array("value" => "39.8元","color" => "#FF0000"),
            "keynote3" => array("value" => "2014年9月16日", "color" => "#888888"),
            "remark"   => array("value" => "欢迎再次购买！", "color" => "#5599FF"),
        );
        ```

关于模板消息的使用请参考 [微信官方文档](http://mp.weixin.qq.com/wiki/)
