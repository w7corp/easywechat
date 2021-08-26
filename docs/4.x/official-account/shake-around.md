# 摇一摇周边


摇一摇周边是微信在线下的全新功能, 为线下商户提供近距离连接用户的能力, 并支持线下商户向周边用户提供个性化营销、互动及信息推荐等服务。

## 获取实例

```php
$shakearound = $app->shake_around;

```

## 说明

> 特别提醒：
1、下述所有的接口调用的方法参数都要严格按照方法参数前的类型传入相应类型的实参，否则可能会得到非预期的结果。
2、涉及需要传入设备id（$deviceIdentifier）的参数时，该参数是一个以 `device_id` 或包含 `uuid` `major` `minor` 为key的关联数组。
3、涉及需要传入设备id列表（$deviceIdentifiers）的参数时，该参数是一个二维数组，第一层为索引类型，第二层为关联类型（$deviceIdentifier）。

```php
// 参数 $deviceIdentifier 的实参形式：
['device_id' => 10097]
// 或
[
    'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
    'major' => 10001,
    'minor' => 12102,
]
// 参数$deviceIdentifiers的实参形式：
[
    ['device_id' => 10097],
    ['device_id' => 10098],
]
// 或
[
    [
        'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
        'major' => 10001,
        'minor' => 12102,
    ],
    [
        'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
        'major' => 10001,
        'minor' => 12103,
    ]
]
```

## 开通摇一摇周边

> 提示：
若不是做 [公众号第三方平台](https://open.weixin.qq.com/cgi-bin/frame?t=home/wx_plugin_tmpl&lang=zh_CN) 开发，建议直接在微信管理后台申请开通摇一摇周边功能。

### 申请开通

申请开通摇一摇周边功能。成功提交申请请求后，工作人员会在三个工作日内完成审核。若审核不通过，可以重新提交申请请求。若是审核中，请耐心等待工作人员审核，在审核中状态不能再提交申请请求。

方法

```php
$shakearound->register($data)
```

>  注意：
1、相关资质文件的图片是使用本页面下方的素材管理的接口上传的，切勿和另一个 [素材管理](material) 接口混淆。
2、行业代码请务必传入**字符串**类型的实参，否则以数字0开头的行业代码将会被当成八进制数处理（将转换为十进制数），这可能不是期望的。

### 查询审核状态

查询已经提交的开通摇一摇周边功能申请的审核状态。在申请提交后，工作人员会在三个工作日内完成审核。

方法

```php
$shakearound->status()
```

### 获取摇一摇的设备及用户信息

获取设备信息，包括UUID、major、minor，以及距离、openID等信息。

方法

```php
$shakearound->user($ticket);
// 或者需要返回门店poi_id
$shakearound->user($ticket, true);
```

## 设备管理

### 申请设备 ID

申请配置设备所需的UUID、Major、Minor。申请成功后返回批次ID，可用返回的批次ID通过“查询设备ID申请状态”接口查询目前申请的审核状态。
一个公众账号最多可申请100000个设备ID，如需申请的设备ID数超过最大限额，请邮件至zhoubian@tencent.com，邮件格式如下：

> 标题：申请提升设备ID额度
内容：
1、公众账号名称及appid（wx开头的字符串，在mp平台可查看）
2、用途
3、预估需要多少设备ID

方法

```php
$shakearound->device->apply($data)
```

### 查询设备 ID 申请审核状态

查询设备ID申请的审核状态。若单次申请的设备ID数量小于等于500个，系统会进行快速审核；若单次申请的设备ID数量大于500个，则在三个工作日内完成审核。

方法

```php
$shakearound->device->status($applyId) // $applyId 批次ID，申请设备ID时所返回的批次ID
```

### 编辑设备信息

> 仅能修改设备的备注信息。

方法

```php
$shakearound->device->update(array $deviceIdentifier, string $comment)
```

参数

> $deviceIdentifier 设备id，设备编号device_id或UUID、major、minor的关联数组，若二者都填，则以设备编号为优先
$comment 设备的备注信息，不超过15个汉字或30个英文字母

示例

```php
$result = $shakearound->device->update(['device_id' => 10011], 'test');
// 或
$result = $shakearound->device->update(['uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
                                          'major' => 1002,
                                          'minor' => 1223,
], 'test');

/* 返回结果
{
    "data": {
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
var_dump($result['errcode']) // 0
```

### 配置设备与门店/其他公众账号门店的关联关系

关联本公众账号门店时，支持创建门店后直接关联在设备上，无需为审核通过状态，摇周边后台自动更新门店的最新信息和状态。
关联其他公众账号门店时，支持设备关联其他公众账号的门店，门店需为审核通过状态。

> 因为第三方门店不归属本公众账号，所以未保存到设备详情中，查询设备列表接口与获取摇周边的设备及用户信息接口不会返回第三方门店。

方法

```php
$shakearound->device->bindPoi(array $deviceIdentifier, $poiId)

//或者 绑定第三方
$shakearound->device->bindThirdPoi(array $deviceIdentifier, $poiId, $poiAppId)
```

参数

> $deviceIdentifier 设备 id，设备编号 device_id 或 UUID、major、minor 的关联数组，若二者都填，则以设备编号为优先
$poiId 设备关联的门店 ID，关联门店后，在门店 1KM 的范围内有优先摇出信息的机会。当值为0时，将清除设备已关联的门店 ID
$poiAppId 关联门店所归属的公众账号的 APP ID

示例

```php
// 关联本公众账号门店
$result = $shakearound->device->bindLocation(['device_id' => 10011], 1231);
// 或
$result = $shakearound->device->bindLocation([
  'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
  'major' => 1002,
  'minor' => 1223,
], 1231);

// 关联其他公众账号门店
// wxappid 为关联门店所归属的公众账号的 APP ID
$result = $shakearound->device->bindThirdPoi(['device_id' => 10011], 1231, 'wxappid');

// 或
$result = $shakearound->device->bindThirdPoi([
  'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
  'major' => 1002,
  'minor' => 1223,
], 1231, 'wxappid');

/* 返回结果
{
    "data": {
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
```

## 查询设备列表

查询已有的设备 ID、UUID、Major、Minor、激活状态、备注信息、关联门店、关联页面等信息。

### 根据设备id批量取回设备数据

方法

> $shakearound->device->listByIds(array $deviceIdentifiers)

参数

> $deviceIdentifiers 设备id列表

示例

```php
$result = $shakearound->device->listByIds([
  ['device_id' => 10097],
  ['device_id' => 10098],
]);
// 或
$result = $shakearound->device->listByIds([
  [
    'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
   'major' => 10001,
   'minor' => 12102,
  ],
  [
    'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
   'major' => 10001,
   'minor' => 12103,
  ]
]);

/* 返回结果
{
    "data": {
        "devices": [
            {
                "comment": "",
                "device_id": 10097,
                "major": 10001,
                "minor": 12102,
                "status": 1,
                "last_active_time":1437276018,
                "poi_id": 0,
                "uuid": "FDA50693-A4E2-4FB1-AFCF-C6EB07647825"
            },
            {
                "comment": "",
                "device_id": 10098,
                "major": 10001,
                "minor": 12103,
                "status": 1,
                "last_active_time":1437276018,
                "poi_appid":"wxe3813f5d8c546fc7"
                "poi_id": 123,
                "uuid": "FDA50693-A4E2-4FB1-AFCF-C6EB07647825"
            }
        ],
        "total_count": 151
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
```

### 分页批量取回设备数据

方法

```php
$shakearound->device->list(int $lastId, int $count)
```

参数

> $lastId 前一次查询列表末尾的设备编号 device_id，第一次查询 lastId 为 0
$count 待查询的设备数量，不能超过50个

示例

```php
$result = $shakearound->device->list(10097, 3);

// 返回结果同上
```

### 根据申请时的批次 ID 分页批量取回设备数据

方法

> $shakearound->device->listByApplyId(int $applyId, int $lastId, int $count)

参数

> $applyId 批次ID，申请设备ID时所返回的批次ID
$lastId 前一次查询列表末尾的设备编号device_id，第一次查询 lastId 为 0
$count 待查询的设备数量，不能超过50个

示例

```php
$result = $shakearound->device->listByApplyId(1231, 10097, 3);

// 返回结果同上
```

## 页面管理

### 新增页面

新增摇一摇出来的页面信息，包括在摇一摇页面出现的主标题、副标题、图片和点击进去的超链接。其中，图片必须为用素材管理接口上传至微信侧服务器后返回的链接。

>
图片是使用本页面下方的素材管理的接口上传的，切勿和另一个 [素材管理](material) 接口混淆。

方法

```php
$shakearound->page->create($data)
```

参数

> $title 在摇一摇页面展示的主标题，不超过6个汉字或12个英文字母
$description 在摇一摇页面展示的副标题，不超过7个汉字或14个英文字母
$pageUrl 点击进去的超链接
$iconUrl 在摇一摇页面展示的图片。图片需先上传至微信侧服务器，用“素材管理-上传图片素材”接口上传图片，返回的图片URL再配置在此处
$comment 可选，页面的备注信息，不超过15个汉字或30个英文字母

示例

```php
$result = $shakearound->page->create($data);

/* 返回结果
{
   "data": {
       "page_id": 28840
   }
   "errcode": 0,
   "errmsg": "success."
}
*/
```

### 编辑页面信息

编辑摇一摇出来的页面信息，包括在摇一摇页面出现的主标题、副标题、图片和点击进去的超链接。

方法

```php
$shakearound->page->update(int $pageId, array $data)
```

参数

> $pageId 摇周边页面唯一ID
$data 需要更新的信息

示例

```php
$result = $shakearound->page->update(28840, [
    'title' => '主标题',
    'description' => '副标题',
    //...
]);
```

## 查询页面列表

查询已有的页面，包括在摇一摇页面出现的主标题、副标题、图片和点击进去的超链接。

### 根据页面id批量取回页面数据

方法

```php
$shakearound->page->listByIds(array $pageIds)
```

参数

> $pageIds 页面的id列表，索引数组

示例

```php
$result = $shakearound->page->listByIds([28840, 28842]);

/* 返回结果
{
   "data": {
       "pages": [
           {
               "comment": "just for test",
               "description": "test",
               "icon_url": "https://www.baidu.com/img/bd_logo1",
               "page_id": 28840,
               "page_url": "http://xw.qq.com/testapi1",
               "title": "测试1"
           },
           {
               "comment": "just for test",
               "description": "test",
               "icon_url": "https://www.baidu.com/img/bd_logo1",
               "page_id": 28842,
               "page_url": "http://xw.qq.com/testapi2",
               "title": "测试2"
           }
       ],
       "total_count": 2
   },
   "errcode": 0,
   "errmsg": "success."
}
*/
```

### 分页批量取回页面数据

方法

```php
$shakearound->page->list(int $begin, int $count)
```

参数

> $begin 页面列表的起始索引值
$count 待查询的页面数量，不能超过50个

示例

```php
$result = $shakearound->page->list(0,2);

// 返回结果同上
```

### 删除页面

删除已有的页面，包括在摇一摇页面出现的主标题、副标题、图片和点击进去的超链接。

>
只有页面与设备没有关联关系时，才可被删除。

方法

```php
$shakearound->page->delete(int $pageId)
```

参数

> $pageId 页面的id

示例

```php
$result = $shakearound->page->delete(34567);

/* 返回结果
{
    "data": {
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
```

### 素材管理

上传在摇一摇功能中需使用到的图片素材，素材保存在微信侧服务器上。图片格式限定为：jpg,jpeg,png,gif。
若图片为在摇一摇页面展示的图片，则其素材为 `icon` 类型的图片，图片大小建议 `120px*120 px` ，限制不超过 `200 px *200 px` ，图片需为 `正方形` 。
若图片为申请开通摇一摇周边功能需要上传的资质文件图片，则其素材为 `license` 类型的图片，图片的文件大小不超过 `2MB` ，尺寸不限，形状不限。

方法

> $shakearound->material->uploadImage(string $path [, string $type = 'icon'])

参数

> $path 图片所在路径
$type 可选，值为icon或license

示例

```php
$result = $shakearound->material->uploadImage(__DIR__ . '/stubs/image.jpg');

/* 返回结果
{
    "data": {
        "pic_url": http://shp.qpic.cn/wechat_shakearound_pic/0/1428377032e9dd2797018cad79186e03e8c5aec8dc/120"
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
```

## 管理设备与页面的关系

通过接口申请的设备ID，需先配置页面，若未配置页面，则摇不出页面信息。

### 配置设备与页面的关联关系

配置完成后，在此设备的信号范围内，即可摇出关联的页面信息。
若设备配置多个页面，则随机出现页面信息。一个设备最多可配置30个关联页面。

>
1、配置时传入该设备需要关联的页面的id列表，该设备原有的关联关系将被直接清除。
2、页面的id列表允许为空（**传入空数组**），当页面的id列表为空时则会清除该设备的所有关联关系。

方法

> $shakearound->relation->bindPage(array $deviceIdentifier, array $pageIds)

参数

> $deviceIdentifier 设备id，设备编号device_id或UUID、major、minor的关联数组，若二者都填，则以设备编号为优先
$pageIds 页面的id列表，索引数组

示例

```php
$result = $shakearound->relation->bindPage(['device_id' => 10011], [12345, 23456, 334567]);
// 或
$result = $shakearound->relation->bindPage(['uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
                                              'major' => 1002,
                                              'minor' => 1223,
], [12345, 23456, 334567]);

/* 返回结果
{
    "data": {
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
var_dump($result->errcode) // 0
```

### 查询设备与页面的关联关系

#### 查询指定设备所关联的页面

根据设备ID或完整的UUID、Major、Minor查询该设备所关联的所有页面信息

方法

> $shakearound->relation->listByDeviceId(array $deviceIdentifier [, boolean $raw = false])

>
该方法默认对返回的数据进行处理后返回一个包含页面id的索引数组。若要返回和 `getDeviceByPageId` 方法类似的数据，请传入 `true` 作为第二个参数。

参数

> $deviceIdentifier 设备id，设备编号device_id或UUID、major、minor的关联数组，若二者都填，则以设备编号为优先
$raw 可选，当为true时，返回值和getDeviceByPageId方法类似，否则返回页面的id列表（索引数组，无关联时为空数组）

示例

```php
$result = $shakearound->relation->listByDeviceId(['device_id' => 10011]);
// 或
$result = $shakearound->relation->listByDeviceId([
  'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
  'major' => 1002,
  'minor' => 1223,
]);

// 返回结果
var_dump($result) // [50054,50055]
```

##### 查询指定页面所关联的设备

指定页面ID分页查询该页面所关联的所有的设备信息

方法

> $shakearound->relation->listByPageId(int $pageId, int $begin, int $count)

参数

> $pageId 指定的页面id
$begin 关联关系列表的起始索引值
$count 待查询的关联关系数量，不能超过50个

示例

```php
$result = $shakearound->relation->listByPageId(50054, 0, 3);

/* 返回结果
{
  "data": {
      "relations": [
          {
              "device_id": 797994,
              "major": 10001,
              "minor": 10023,
              "page_id": 50054,
              "uuid": "FDA50693-A4E2-4FB1-AFCF-C6EB07647825"
          },
          {
              "device_id": 797995,
              "major": 10001,
              "minor": 10024,
              "page_id": 50054,
              "uuid": "FDA50693-A4E2-4FB1-AFCF-C6EB07647825"
          }
      ],
      "total_count": 2
  },
  "errcode": 0,
  "errmsg": "success."
}
*/
```

### 摇一摇数据统计

> 此接口无法获取当天的数据，最早只能获取前一天的数据。
由于系统在凌晨处理前一天的数据，太早调用此接口可能获取不到数据，建议在早上8：00之后调用此接口。

### 以设备为维度的数据统计

查询单个设备进行摇周边操作的人数、次数，点击摇周边消息的人数、次数。

>
查询的最长时间跨度为 30 天。只能查询最近 90 天的数据。

方法

> $shakearound->stats->deviceSummary(array $deviceIdentifier, int $beginDate, int $endDate)

参数

> $deviceIdentifier 设备id，设备编号device_id或UUID、major、minor的关联数组，若二者都填，则以设备编号为优先
$beginDate 起始日期时间戳，最长时间跨度为30天，单位为秒
$endDate 结束日期时间戳，最长时间跨度为30天，单位为秒

示例

```php
$result = $shakearound->stats->deviceSummary(['device_id' => 10011], 1425052800, 1425139200);
// 或
$result = $shakearound->stats->deviceSummary(['uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
                                                'major' => 1002,
                                                'minor' => 1223,
], 1425052800, 1425139200);

/* 返回结果
{
   "data": [
       {
           "click_pv": 0,
           "click_uv": 0,
           "ftime": 1425052800,
           "shake_pv": 0,
           "shake_uv": 0
       },
       {
           "click_pv": 0,
           "click_uv": 0,
           "ftime": 1425139200,
           "shake_pv": 0,
           "shake_uv": 0
       }
   ],
   "errcode": 0,
   "errmsg": "success."
}
*/
```

### 批量查询设备统计数据

查询指定时间商家帐号下的每个设备进行摇周边操作的人数、次数，点击摇周边消息的人数、次数。

> 只能查询最近90天内的数据，且一次只能查询一天。

>
对于摇周边人数、摇周边次数、点击摇周边消息的人数、点击摇周边消息的次数都为0的设备，不在结果列表中返回。

方法

```php
$shakearound->stats->devicesSummary(int $timestamp, int $pageIndex)
```

参数

> $timestamp 指定查询日期时间戳，单位为秒
$pageIndex 指定查询的结果页序号，返回结果按摇周边人数降序排序，每50条记录为一页

示例

```php
$result = $shakearound->stats->devicesSummary(1435075200, 1);

/* 返回结果
{
    "data": {
        "devices": [
            {
                "device_id": 10097,
                "major": 10001,
                "minor": 12102,
                "uuid": "FDA50693-A4E2-4FB1-AFCF-C6EB07647825"
                "shake_pv": 1
                "shake_uv": 2
                "click_pv": 3
                "click_uv": 4
            },
            {
                "device_id": 10098,
                "major": 10001,
                "minor": 12103,
                "uuid": "FDA50693-A4E2-4FB1-AFCF-C6EB07647825"
                "shake_pv": 1
                "shake_uv": 2
                "click_pv": 3
                "click_uv": 4
            }
        ],
    },
    "date":1435075200
    "total_count": 151
    "page_index":1
    "errcode": 0,
    "errmsg": "success."
}
*/
```

### 以页面为维度的数据统计

查询单个页面通过摇周边摇出来的人数、次数，点击摇周边页面的人数、次数

> 注意：
查询的最长时间跨度为30天。只能查询最近90天的数据。

方法

```php
$shakearound->stats->pageSummary(int $pageId, int $beginDate, int $endDate);
```

参数

> $pageId 指定页面的页面ID
$beginDate 起始日期时间戳，最长时间跨度为30天，单位为秒
$endDate 结束日期时间戳，最长时间跨度为30天，单位为秒

示例

```php
$result = $shakearound->stats->pageSummary(12345, 1425052800, 1425139200);

/* 返回结果
{
   "data": [
       {
           "click_pv": 0,
           "click_uv": 0,
           "ftime": 1425052800,
           "shake_pv": 0,
           "shake_uv": 0
       },
       {
           "click_pv": 0,
           "click_uv": 0,
           "ftime": 1425139200,
           "shake_pv": 0,
           "shake_uv": 0
       }
   ],
   "errcode": 0,
   "errmsg": "success."
}
*/
```
### 批量查询页面统计数据

查询指定时间商家帐号下的每个页面进行摇周边操作的人数、次数，点击摇周边消息的人数、次数。

>
对于摇周边人数、摇周边次数、点击摇周边消息的人数、点击摇周边消息的次数都为0的页面，不在结果列表中返回。

方法

```php
$shakearound->stats->pagesSummary(int $timestamp, int $pageIndex);
```

参数

> $timestamp 指定查询日期时间戳，单位为秒
$pageIndex 指定查询的结果页序号，返回结果按摇周边人数降序排序，每50条记录为一页

示例

```php
$result = $shakearound->stats->pagesSummary(1435075200, 1);

/* 返回结果
{
    "data": {
        "pages": [
            {
                "page_id":1234
                "click_pv": 1,
                "click_uv": 3,
                "shake_pv": 0,
                "shake_uv": 0
            },
            {
                "page_id":5678
                "click_pv": 1,
                "click_uv": 2,
                "shake_pv": 0,
                "shake_uv": 0
            },
        ],
    },
    "date":1435075200
    "total_count": 151
    "page_index":1
    "errcode": 0,
    "errmsg": "success."
}
*/
```

## 设备分组管理

调用H5页面获取设备信息 JS API接口，需要先把设备分组，微信客户端只会返回已在分组中的设备信息。

### 新增分组

新建设备分组，每个帐号下最多只有1000个分组。

方法

```php
$shakearound->group->create(string $name)

参数

> $name 分组名称，不超过100汉字或200个英文字母

示例

```php
$result = $shakearound->group->create('test');

/* 返回结果
{
  "data": {
      "group_id" : 123,
      "group_name" : "test"
  },
  "errcode": 0,
  "errmsg": "success."
}
*/
```

### 编辑分组信息

编辑设备分组信息，目前只能修改分组名。

方法

> $shakearound->group->update(int $groupId, string $name)

参数

> $groupId 分组唯一标识，全局唯一
$name 分组名称，不超过100汉字或200个英文字母

示例

```php
$result = $shakearound->group->update(123, 'newName');

/* 返回结果
{
    "data": {
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
```

### 删除分组

删除设备分组，若分组中还存在设备，则不能删除成功。需把设备移除以后，才能删除。

> 在执行删除前，最好先使用 `get` 方法查询分组详情，若分组内有设备，先使用 `removeDevices` 方法移除。

方法

```php
$shakearound->group->delete(int $groupId)
```

参数

> $groupId 分组唯一标识，全局唯一

示例

```php
$result = $shakearound->group->delete(123);

/* 返回结果
{
    "data": {
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
```

### 查询分组列表

查询账号下所有的分组。

方法

```php
$shakearound->group->list(int $begin, int $count)
```

参数

> $begin 分组列表的起始索引值
$count 待查询的分组数量，不能超过1000个

示例

```php
$result = $shakearound->group->list(0, 2);

/* 返回结果
{
    "data": {
        "groups":[
            {
                "group_id" : 123,
                "group_name" : "test1"
            },
            {
                "group_id" : 124,
                "group_name" : "test2"
            }
        ],
        "total_count": 100
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
```

### 查询分组详情

查询分组详情，包括分组名，分组id，分组里的设备列表。

方法

```php
$shakearound->group->get(int $groupId, int $begin, int $count)
```

参数

> $groupId 分组唯一标识，全局唯一
$begin 分组里设备的起始索引值
$count 待查询的分组里设备的数量，不能超过1000个

示例

```php
$result = $shakearound->group->get(123, 0, 2);

/* 返回结果
{
    "data": {
        "group_id" : 123,
        "group_name" : "test",
        "total_count": 100,
        "devices" :[
            {
                "device_id" : 123456,
                "uuid" : "FDA50693-A4E2-4FB1-AFCF-C6EB07647825",
                "major" : 10001,
                "minor" : 10001,
                "comment" : "test device1",
                "poi_id" : 12345,
            },
            {
                "device_id" : 123457,
                "uuid" : "FDA50693-A4E2-4FB1-AFCF-C6EB07647825",
                "major" : 10001,
                "minor" : 10002,
                "comment" : "test device2",
                "poi_id" : 12345,
            }
        ]
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
```

### 添加设备到分组

添加设备到分组，每个分组能够持有的设备上限为10000，并且每次添加操作的添加上限为1000。

> 只有在摇周边申请的设备才能添加到分组。

方法

> $shakearound->group->addDevices(int $groupId, array $deviceIdentifiers)

参数

> $groupId 分组唯一标识，全局唯一
$deviceIdentifiers 设备id列表

示例

```php
$result = $shakearound->group->addDevices(123, [
  ['device_id' => 10097],
  ['device_id' => 10098],
]);

// 或
$result = $shakearound->group->addDevices(123, [
  [
    'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
    'major' => 10001,
    'minor' => 12102,
  ],
  [
    'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
    'major' => 10001,
    'minor' => 12103,
  ]
]);

/* 返回结果
{
    "data": {
    },
    "errcode": 0,
    "errmsg": "success."
}
*/
```

### 从分组中移除设备

从分组中移除设备，每次删除操作的上限为 1000。

方法

```php
$shakearound->group->removeDevices(int $groupId, array $deviceIdentifiers)
```

参数

> $groupId 分组唯一标识，全局唯一
$deviceIdentifiers 设备id列表

示例

```php
$result = $shakearound->group->removeDevices(123, [
  ['device_id' => 10097],
  ['device_id' => 10098],
]);
// 或
$result = $shakearound->group->removeDevices(123, [
    [
      'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
      'major' => 10001,
      'minor' => 12102,
    ],
    [
      'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
      'major' => 10001,
      'minor' => 12103,
    ]
]);
```

## 摇一摇事件通知

用户进入摇一摇界面，在“周边”页卡下摇一摇时，微信会把这个事件推送到开发者填写的URL（登录公众平台进入开发者中心设置）。推送内容包含摇一摇时“周边”页卡展示出来的页面所对应的设备信息，以及附近最多五个属于该公众账号的设备的信息。当摇出列表时，此事件不推送。

> 摇一摇事件的事件类型：ShakearoundUserShake

关于事件的处理请移步： [事件](events)

