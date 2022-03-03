# 返佣商品

> 微信文档：https://mp.weixin.qq.com/cgi-bin/announce?action=getannouncement&key=11533749572M9ODP&version=1&lang=zh_CN&platform=2

## 导入商品

每次调用支持批量导入不超过1000条的商品信息。每分钟单个商户全局调用次数不得超过200次。每天调用次数不得超过100万次。每次请求包大小不超过2M。

```php
$data = [
    [
        'pid' => 'pid001',
        'image_info' => [
            'main_image_list' => [
                [
                    'url' => 'http://www.google.com/a.jpg',
                ],
                [
                    'url' => 'http://www.google.com/b.jpg',
                ],
            ],
        ],
        
        //...
    ],
    
    //...
];

$result = $app->goods->add($data);

// $result:
//{
//    "errcode": 0,
//    "errmsg": "ok",
//    "status_ticket": "115141102647330200"
//}
```

`status_ticket` 用于获取此次导入的详细结果。

## 更新商品

更新时，字段不填代表不更新该字段（此处的字段不填，代表无此字段，而不是把字段的值设为空，设为空即代表更新该字段为空）。

对于字符串类型的选填字段，如副标题，若清空不展示，则可设置为空；对于数字类型的选填字段，如原价，若清空不展示，则需设置为0。

> 基本字段更新中 `pid` 为必填字段，且无法修改

```php
$data = [
    [
        'pid' => 'pid001',
        'image_info' => [
            'main_image_list' => [
                [
                    'url' => 'http://www.baidu.com/c.jpg',
                ],
                [
                    'url' => 'http://www.baidu.com/d.jpg',
                ],
            ],
        ],
        
        //...
    ],
    
    //...
];
 
$result = $app->goods->update($data);
 
// $result:
//{
//    "errcode": 0,
//    "errmsg": "ok",
//    "status_ticket": "115141102647330200"
//}
```

> 说明：导入商品和更新商品使用的是同一个接口。
 
## 查询导入/更新商品状态
 
用于查询导入或更新商品的结果，当导入或更新商品失败时，若为系统错误可进行重试；若为其他错误，请排查解决后进行重试。

```php
$status_ticket = '115141102647330200';

$result = $app->goods->status($status_ticket);

// $result:
//{
//    "errcode": 0,
//    "errmsg": "ok",
//    "result": {
//        "succ_cnt": 2,
//        "fail_cnt": 0,
//        "total_cnt": 2,
//        "progress": "100.00%",
//        "statuses": [
//            {
//                "pid": "pid001",
//                "ret": 0,
//                "err_msg": "success",
//                "err_msg_zh_cn": "成功"
//            },
//            {
//                "pid": "pid002",
//                "ret": 0,
//                "err_msg": "success",
//                "err_msg_zh_cn": "成功"
//            }
//        ]
//    }
//}
```

## 获取单个商品信息

使用该接口获取已导入的商品信息，供验证信息及抽查导入情况使用。

```php
$pid = 'pid001';

$app->goods->get($pid);
```

> 返回结果中的 `product` 字段内容与 `导入商品接口` 字段一致，导入时未设置的值有可能获取时仍会返回，但显示为空

## 分页获取商品信息

使用该接口可获取已导入的全量商品信息，供全量验证信息使用。

```php
$context = '';  // page 为 1 时传空即可。当 page 大于 1 时必填，填入上一次访问本接口返回的 page_context。
$page = 1;      // 页码
$size = 10;     // 每页数据大小，目前限制为100以内，注意一次全量验证过程中该参数的值需保持不变

$app->goods->list($context, $page, $size);
```

> 返回结果中的 `product` 字段内容与 `导入商品接口` 字段一致，导入时未设置的值有可能获取时仍会返回，但显示为空。
> `page_context` 字段用于获取下一页数据时使用。
