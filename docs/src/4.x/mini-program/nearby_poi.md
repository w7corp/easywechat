# 附近的小程序

> 微信文档：https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/nearby-poi/nearbyPoi.add.html

## 添加地点

```php
$params = [
    'kf_info' => '{"open_kf":true,"kf_headimg":"http://mmbiz.qpic.cn/mmbiz_jpg/kKMgNtnEfQzDKpLXYhgo3W3Gndl34gITqmP914zSwhajIEJzUPpx40P7R8fRe1QmicneQMhFzpZNhSLjrvU1pIA/0?wx_fmt=jpeg","kf_name":"Harden"}',
    'pic_list' => '{"list":["http://mmbiz.qpic.cn/mmbiz_jpg/kKMgNtnEfQzDKpLXYhgo3W3Gndl34gITqmP914zSwhajIEJzUPpx40P7R8fRe1QmicneQMhFzpZNhSLjrvU1pIA/0?wx_fmt=jpeg","http://mmbiz.qpic.cn/mmbiz_jpg/kKMgNtnEfQzDKpLXYhgo3W3Gndl34gITRneE5FS9uYruXGMmrtmhsBySwddEWUGOibG8Ze2NT5E3Dyt79I0htNg/0?wx_fmt=jpeg"]}',
    'service_infos' => '{"service_infos":[{"id":2,"type":1,"name":"快递","appid":"wx1373169e494e0c39","path":"index"},{"id":0,"type":2,"name":"自定义","appid":"wx1373169e494e0c39","path":"index"}]}',
    'store_name' => '羊村小马烧烤',
    'contract_phone' => '111111111',
    'hour' => '00:00-11:11',
    'company_name' => '深圳市腾讯计算机系统有限公司',
    'credential' => '156718193518281',
    'address' => '新疆维吾尔自治区克拉玛依市克拉玛依区碧水路15-1-8号(碧水云天广场)',
    'qualification_list' => '3LaLzqiTrQcD20DlX_o-OV1-nlYMu7sdVAL7SV2PrxVyjZFZZmB3O6LPGaYXlZWq',
];

$app->nearby_poi->add($params);
```

## 更新地点

```php
$poiId = 'xxxxxxxx';

$params = [
    'kf_info' => '{"open_kf":true,"kf_headimg":"http://mmbiz.qpic.cn/mmbiz_jpg/kKMgNtnEfQzDKpLXYhgo3W3Gndl34gITqmP914zSwhajIEJzUPpx40P7R8fRe1QmicneQMhFzpZNhSLjrvU1pIA/0?wx_fmt=jpeg","kf_name":"Harden"}',
    'pic_list' => '{"list":["http://mmbiz.qpic.cn/mmbiz_jpg/kKMgNtnEfQzDKpLXYhgo3W3Gndl34gITqmP914zSwhajIEJzUPpx40P7R8fRe1QmicneQMhFzpZNhSLjrvU1pIA/0?wx_fmt=jpeg","http://mmbiz.qpic.cn/mmbiz_jpg/kKMgNtnEfQzDKpLXYhgo3W3Gndl34gITRneE5FS9uYruXGMmrtmhsBySwddEWUGOibG8Ze2NT5E3Dyt79I0htNg/0?wx_fmt=jpeg"]}',
    'service_infos' => '{"service_infos":[{"id":2,"type":1,"name":"快递","appid":"wx1373169e494e0c39","path":"index"},{"id":0,"type":2,"name":"自定义","appid":"wx1373169e494e0c39","path":"index"}]}',
    'contract_phone' => '111111111',
    'hour' => '00:00-11:11',
    'company_name' => '深圳市腾讯计算机系统有限公司',
    'credential' => '156718193518281',
    'address' => '新疆维吾尔自治区克拉玛依市克拉玛依区碧水路15-1-8号(碧水云天广场)',
    'qualification_list' => '3LaLzqiTrQcD20DlX_o-OV1-nlYMu7sdVAL7SV2PrxVyjZFZZmB3O6LPGaYXlZWq',
];

$app->nearby_poi->update($poiId, $params);
```

## 删除地点

```php
$poiId = 'xxxxxxxx';

$app->nearby_poi->delete($poiId);
```

## 地点列表

```php
$page = 1;
$pageRows = 10;

$app->nearby_poi->list($page, $pageRows);
```

## 设置地点展示状态

```php
$poiId = 'xxxxxxxx';
$status = 0; // 0: 不展示，1：展示

$app->nearby_poi->setVisibility($poiId, $status);
```
