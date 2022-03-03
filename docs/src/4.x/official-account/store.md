# 门店小程序

## 拉取门店小程序类目

```php
$app->store->categories();
```

## 创建门店小程序

> 说明：创建门店小程序提交后需要公众号管理员确认通过后才可进行审核。如果主管理员 24 小时超时未确认，才能再次提交。

```php
$app->store->createMerchant($baseInfo);
```

> - `$baseInfo` 为门店小程序的基本信息数组，**`qualification_list` 字段为类目相关证件的临时素材 `mediaid` 如果 `second_catid` 对应的 `sensitive_type` 为 1 ，则 `qualification_list` 字段需要填 支持 0~5 个 `mediaid`，例如 `mediaid1`。`headimg_mediaid` 字段为头像 --- 临时素材 `mediaid`。`mediaid` 用现有的 `media/upload` 接口得到的,获取链接： [临时素材](../basic-services/media.md) ( 支持 PNG\JPEG\JPG\GIF 格式的图片，后续加上其他格式)**

示例：

```php

$info = [
    "first_catid"        => 476, //categories 接口获取的一级类目id
    "second_catid"       => 477, //categories 接口获取的二级类目id
    "qualification_list" =>  "RTZgKZ386yFn5kQSWLTxe4bqxwgzGBjs3OE02cg9CVQk1wRVE3c8fjUFX7jvpi-P",
    "headimg_mediaid"    => "RTZgKZ386yFn5kQSWLTxe4bqxwgzGBjs3OE02cg9CVQk1wRVE3c8fjUFX7jvpi-P",
    "nickname"           => "hardenzhang308",
    "intro"              => "hardenzhangtest",
    "org_code"           => "",
    "other_files"        => ""
];

$result = $app->store->createMerchant($info);
```

> 注意：创建门店小程序的审核结果,会以事件形式推送给商户填写的回调 URL

## 查询门店小程序审核结果

```php
$app->store->getStatus($baseInfo);
```

## 修改门店小程序信息

```php
$app->store->updateMerchant($data);
```

> - `$data` 需要更新的部分数据，目前仅支持门店头像和门店小程序介绍，**若有填写内容则为覆盖更新,若无内容则视为不修改,维持原有内容。`headimg_mediaid`、`intro` 字段参考创建门店小程序**

示例：

```php
$data = [
    "headimg_mediaid" => "RTZgKZ386yFn5kQSWLTxe4bqxwgzGBjs3OE02cg9CVQk1wRVE3c8fjUFX7jvpi-P",
    "intro"           => "麦辣鸡腿堡套餐,麦乐鸡,全家桶",
];

$result = $app->store->updateMerchant($data);
```

## 从腾讯地图拉取省市区信息

```php
$app->store->districts();
```

## 在腾讯地图中搜索门店

```php
$app->store->searchFromMap($districtId, $keyword);
```

> - `$districtId` 为从腾讯地图拉取的地区 `id`
> - `$keyword` 为搜索的关键词

## 在腾讯地图中创建门店

```php
$app->store->createFromMap($baseInfo);
```

示例：

```php
$baseInfo = [
    "name"       => "hardenzhang",
    "longitude"  => "113.323753357",
    "latitude"   => "23.0974903107",
    "province"   => "广东省",
    "city"       => "广州市",
    "district"   => "海珠区",
    "address"    => "TIT",
    "category"   => "类目1:类目2",
    "telephone"  => "12345678901",
    "photo"      => "http://mmbiz.qpic.cn/mmbiz_png/tW66AWE2K6ECFPcyAcIZTG8RlcR0sAqBibOm8gao5xOoLfIic9ZJ6MADAktGPxZI7MZLcadZUT36b14NJ2cHRHA/0?wx_fmt=png",
    "license"    => "http://mmbiz.qpic.cn/mmbiz_png/tW66AWE2K6ECFPcyAcIZTG8RlcR0sAqBibOm8gao5xOoLfIic9ZJ6MADAktGPxZI7MZLcadZUT36b14NJ2cHRHA/0?wx_fmt=png",
    "introduct"  => "test",
    "districtid" => "440105",
];
```

> - `$baseInfo`: 门店相关信息

> 事件推送 --- 腾讯地图中创建门店的审核结果。腾讯地图审核周期为 3 个工作日，请在期间内留意审核结果事件推送。提交后未当即返回事件推送即为审核中，请耐心等待。

## 添加门店

```php
$app->store->create($baseInfo);
```

示例：

```php
$baseInfo = [
    "poi_id"             => "",
    "map_poi_id"         => "2880741500279549033",
    "pic_list"           => "['list' => ['http://mmbiz.qpic.cn/mmbiz_jpg/tW66AWvE2K4EJxIYOVpiaGOkfg0iayibiaP2xHOChvbmKQD5uh8ymibbEKlTTPmjTdQ8ia43sULLeG1pT2psOfPic4kTw/0?wx_fmt=jpeg']]",
    "contract_phone"     => "1111222222",
    "credential"         => "22883878-0",
    "qualification_list" => "RTZgKZ386yFn5kQSWLTxe4bqxwgzGBjs3OE02cg9CVQk1wRVE3c8fjUFX7jvpi-P"
];
```

> - `$baseInfo`: 门店相关信息。`pic_list` 门店图片，可传多张图片 `pic_list`

> 事件推送 - 创建门店的审核结果

## 更新门店信息

```php
$app->store->update($baseInfo);
```

> - `$baseInfo`: 门店相关信息。

> 果要更新门店的图片，实际相当于走一次重新为门店添加图片的流程，之前的旧图片会全部废弃。并且如果重新添加的图片中有与之前旧图片相同的，此时这个图片不需要重新审核。
