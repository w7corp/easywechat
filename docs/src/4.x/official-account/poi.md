# 门店

## 创建门店

用 POI 接口新建门店时所使用的图片 url 必须为微信自己域名的 url,因此需要先用上传图片接 口上传图片并获取 url,再创建门店。上传的图片限制文件大小限制 1MB,支持 JPG 格式，图片接口请参考：[临时素材](../basic-services/media.md)

```php
$app->poi->create($baseInfo);
```

> - `$baseInfo` 为门店的基本信息数组

示例：

```php
<?php

$info = array(
         "sid"             => "33788392",
         "business_name"   => "麦当劳",
         "branch_name"     => "艺苑路店",
         "province"        => "广东省",
         "city"            => "广州市",
         "district"        => "海珠区",
         "address"         => "艺苑路 11 号",
         "telephone"       => "020-12345678",
         "categories"      => array("美食,快餐小吃"),
         "offset_type"     => 1,
         "longitude"       => 115.32375,
         "latitude"        => 25.097486,
         "photo_list"      => array(
                               array("photo_url" => "https://XXX.com"),
                               array("photo_url" => "https://XXX.com"),
                             ),
         "recommend"       => "麦辣鸡腿堡套餐,麦乐鸡,全家桶",
         "special"         => "免费 wifi,外卖服务",
         "introduction"    => "麦当劳是全球大型跨国连锁餐厅,1940 年创立于美国,在世界上大约拥有 3  万间分店。主要售卖汉堡包,以及薯条、炸鸡、汽水、冰品、沙拉、水果等 快餐食品",
         "open_time"       => "8:00-20:00",
         "avg_price"       => 35,
    );

$result = $app->poi->create($info); // true or exception
```

> 注意：新创建的门店在审核通过后,会以事件形式推送给商户填写的回调 URL

## 获取指定门店信息

```php
$app->poi->get($poiId);
```

> - `$poiId` 为门店 ID

示例：

```php
$info = $app->poi->get(271262077);
```

## 获取门店列表

```php
$app->poi->list($begin, $limit);// begin:0, limit:10
```

> - `$begin` 就是查询起点，`MySQL` 里的 `offset`；
> - `$limit` 查询条数，同 `MySQL` 里的 `limit`；

> 两参数均可选

示例：

```php
$pois = $app->poi->list(0, 2);// 取2条记录
//
//[
//  {
//    "sid": "100",
//    "poi_id": "271864249",
//    "business_name": "麦当劳",
//    "branch_name": "艺苑路店",
//    "address": "艺苑路 11 号",
//    "available_state": 3
//  },
//  {
//    "sid": "101",
//    "business_name": "麦当劳",
//    "branch_name": "赤岗路店",
//    "address": "赤岗路 102 号",
//    "available_state": 4
//  }
//]
```

## 修改门店信息

商户可以通过该接口,修改门店的服务信息,包括:图片列表、营业时间、推荐、特色服务、简 介、人均价格、电话 7 个字段。目前基础字段包括(名称、坐标、地址等不可修改)。

```php
$app->poi->update($poiId, $data);
```

> - `$poiId` 为门店 ID
> - `$data` 需要更新的部分数据，**若有填写内容则为覆盖更新,若无内容则视为不 修改,维持原有内容。photo_list 字段为全列表覆盖,若需要增加图片,需将之前图片同样放入 list 中,在其后增加新增图片。如:已有 A、B、C 三张图片,又要增加 D、E 两张图,则需要调 用该接口,photo_list 传入 A、B、C、D、E 五张图片的链接。**

示例：

```php
$data = array(
         "telephone" => "020-12345678",
         "recommend" => "麦辣鸡腿堡套餐,麦乐鸡,全家桶",
         //...
        );

$res = $app->poi->update(271262077, $data); //true or exception
```

## 删除门店

```php
$app->poi->delete($poiId);
```

示例：

```php
$app->poi->delete(271262077);// true or exception
```
