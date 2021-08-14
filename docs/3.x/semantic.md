# 语义理解


微信开放平台语义理解接口调用（http请求）简单方便，用户无需掌握语义理解及相关技术，只需根据自己的产品特点，选择相应的服务即可搭建一套智能语义服务。

## 获取实例

```php
<?php

// ... 前面部分省略

$app = new Application($options);

$semantic = $app->semantic;
```

## API

+ `query($keyword, $categories, $other)` 语义理解:

  + `$keyword` 为关键字
  + `$categories` 需要使用的服务类型，数组或者多个用 “，” 隔开字符吕，不能为空;
  + `$other` 为其它属性：
    + `latitude`  `float`  纬度坐标，与经度同时传入；与城市二选一传入
    + `longitude`  `float`  经度坐标，与纬度同时传入；与城市二选一传入
    + `city`   `string`  城市名称，与经纬度二选一传入
    + `region` `string`  区域名称，在城市存在的情况下可省；与经纬度二选一传入
    + `uid`  `string` 用户唯一id（非开发者id），用户区分公众号下的不同用户（建议填入用户openid），如果为空，则无法使用上下文理解功能。appid和uid同时存在的情况下，才可以使用上下文理解功能。

> 注：单类别意图比较明确，识别的覆盖率比较大，所以如果只要使用特定某个类别，建议将category只设置为该类别。

example:

```php
$result = $semantic->query('查一下明天从北京到上海的南航机票', "flight,hotel", array('city' => '北京', 'uid' => '123456'));
// 查询参数：
// {
//    "query":"查一下明天从北京到上海的南航机票",
//    "city":"北京",
//    "category": "flight,hotel",
//    "appid":"wxaaaaaaaaaaaaaaaa",
//    "uid":"123456"
// }
```
返回值示例：

```json
{
    "errcode":0,
    "query":"查一下明天从北京到上海的南航机票",
    "type":"flight",
    "semantic":{
        "details":{
            "start_loc":{
                "type":"LOC_CITY",
                "city":"北京市",
                "city_simple":"北京",
                "loc_ori":"北京"
                },
            "end_loc": {
                "type":"LOC_CITY",
                "city":"上海市",
                "city_simple":"上海",
                "loc_ori":"上海"
              },
            "start_date": {
                "type":"DT_ORI",
                "date":"2014-03-05",
                "date_ori":"明天"
              },
           "airline":"中国南方航空公司"
        },
    "intent":"SEARCH"
}
```

更多详细内容与协议说明，请查看 [微信官方文档](http://mp.weixin.qq.com/wiki/)