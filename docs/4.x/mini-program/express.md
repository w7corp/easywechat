# 物流助手 电子面单

## 获取支持的快递公司列表

> https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/express/by-business/logistics.getAllDelivery.html

```php

$app->express->listProviders();

{
  "count": 8,
  "data": [
    {
      "delivery_id": "BEST",
      "delivery_name": "百世快递"
    },
    ...
  ]
}

```

## 生成运单

> https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/express/by-business/logistics.addOrder.html

```php

$app->express->createWaybill($data);


// 成功返回

{
  "order_id": "01234567890123456789",
  "waybill_id": "123456789",
  "waybill_data": [
    {
      "key": "SF_bagAddr",
      "value": "广州"
    },
    {
      "key": "SF_mark",
      "value": "101- 07-03 509"
    }
  ]
}

// 失败返回

{
  "errcode": 9300501,
  "errmsg": "delivery logic fail",
  "delivery_resultcode": 10002,
  "delivery_resultmsg": "客户密码不正确"
}

```

## 取消运单

> https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/express/by-business/logistics.cancelOrder.html

```php
$app->express->deleteWaybill($data);

```

## 获取运单数据

> https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/express/by-business/logistics.getOrder.html

```php
$app->express->getWaybill($data);

```

## 查询运单轨迹

> https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/express/by-business/logistics.getPath.html

```php
$app->express->getWaybillTrack($data);

```

## 获取电子面单余额。

仅在使用加盟类快递公司时，才可以调用。

> https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/express/by-business/logistics.getQuota.html

```php

$app->express->getBalance($deliveryId, $bizId);

// 例如：

$app->express->getBalance('YTO', 'xyz');
```

## 绑定打印员

若需要使用微信打单 PC 软件，才需要调用。

> https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/express/by-business/logistics.updatePrinter.html

```php
$app->express->bindPrinter($openid);
```

## 解绑打印员

若需要使用微信打单 PC 软件，才需要调用。

> https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/express/by-business/logistics.updatePrinter.html

```php
$app->express->unbindPrinter($openid);
```
