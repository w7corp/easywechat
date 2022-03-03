# 卡券

-

## 获取实例

```php
$card = $app->card;
```

## 通用功能

### 获取卡券颜色

```php
$card->colors();
```

### 卡券开放类目查询

```php
$card->categories();
```

### 创建卡券

创建卡券接口是微信卡券的基础接口，用于创建一类新的卡券，获取 card_id，创建成功并通过审核后，商家可以通过文档提供的其他接口将卡券下发给用户，每次成功领取，库存数量相应扣除。

```php
$card->create($cardType = 'member_card', array $attributes);
```

> - `attributes` array 卡券信息

示例：

```php
<?php

	$cardType = 'GROUPON';

    $attributes = [
      'base_info' => [
          'brand_name' => '微信餐厅',
          'code_type' => 'CODE_TYPE_TEXT',
          'title' => '132元双人火锅套餐',
          //...
      ],
      'advanced_info' => [
          'use_condition' => [
              'accept_category' => '鞋类',
              'reject_category' => '阿迪达斯',
              'can_use_with_other_discount' => true,
          ],
          //...
      ],
    ];

$result = $card->create($cardType, $attributes);
```

### 获取卡券详情

```php
$cardInfo = $card->get($cardId);
```

### 批量查询卡列表

```php
$card->list($offset = 0, $count = 10, $statusList = 'CARD_STATUS_VERIFY_OK');
```

> - `offset` int - 查询卡列表的起始偏移量，从 0 开始
> - `count` int - 需要查询的卡片的数量
> - `statusList` - 支持开发者拉出指定状态的卡券列表，详见 example

示例：

```php
// CARD_STATUS_NOT_VERIFY, 待审核；
// CARD_STATUS_VERIFY_FAIL, 审核失败；
// CARD_STATUS_VERIFY_OK， 通过审核；
// CARD_STATUS_USER_DELETE，卡券被商户删除；
// CARD_STATUS_DISPATCH，在公众平台投放过的卡券；

$result = $card->list($offset, $count, 'CARD_STATUS_NOT_VERIFY');
```

### 更改卡券信息接口

支持更新所有卡券类型的部分通用字段及特殊卡券中特定字段的信息。

```php
$card->update($cardId, $type, $attributes = []);
```

> - `type` string - 卡券类型

示例：

```php
$cardId = 'pdkJ9uCzKWebwgNjxosee0ZuO3Os';

$type = 'groupon';

$attributes = [
  'base_info' => [
    'logo_url' => 'http://mmbiz.qpic.cn/mmbiz/2aJY6aCPatSeibYAyy7yct9zJXL9WsNVL4JdkTbBr184gNWS6nibcA75Hia9CqxicsqjYiaw2xuxYZiaibkmORS2oovdg/0',
    'center_title' => '顶部居中按钮',
    'center_sub_title' => '按钮下方的wording',
    'center_url' => 'http://www.easywechat.com',
    'custom_url_name' => '立即使用',
    'custom_url' => 'http://www.qq.com',
    'custom_url_sub_title' => '6个汉字tips',
    'promotion_url_name' => '更多优惠',
    'promotion_url' => 'http://www.qq.com',
  ],
  //...
];

$result = $card->update($cardId, $type, $attributes);
```

### 删除卡券

```php
$card->delete($cardId);
```

### 创建二维码

开发者可调用该接口生成一张卡券二维码供用户扫码后添加卡券到卡包。

自定义 Code 码的卡券调用接口时，POST 数据中需指定 code，非自定义 code 不需指定，指定 openid 同理。指定后的二维码只能被用户扫描领取一次。

```php
$card->createQrCode($cards);
```

> - `cards` array - 卡券相关信息

示例：

```php
// 领取单张卡券
$cards = [
    'action_name' => 'QR_CARD',
    'expire_seconds' => 1800,
    'action_info' => [
      'card' => [
        'card_id' => 'pdkJ9uFS2WWCFfbbEfsAzrzizVyY',
        'is_unique_code' => false,
        'outer_id' => 1,
      ],
    ],
  ];

$result = $card->createQrCode($cards);
```

```php
// 领取多张卡券
$cards = [
    'action_name' => 'QR_MULTIPLE_CARD',
    'action_info' => [
      'multiple_card' => [
        'card_list' => [
          ['card_id' => 'pdkJ9uFS2WWCFfbbEfsAzrzizVyY'],
        ],
      ],
    ],
  ];

$result = $card->createQrCode($cards);
```

请求成功返回值示例：

```json
{
  "errcode": 0,
  "errmsg": "ok",
  "ticket": "gQHB8DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL0JIV3lhX3psZmlvSDZmWGVMMTZvAAIEsNnKVQMEIAMAAA==", //获取ticket后需调用换取二维码接口获取二维码图片，详情见字段说明。
  "expire_seconds": 1800,
  "url": "http://weixin.qq.com/q/BHWya_zlfioH6fXeL16o ",
  "show_qrcode_url": "https://mp.weixin.qq.com/cgi-bin/showqrcode?  ticket=gQH98DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL0czVzRlSWpsamlyM2plWTNKVktvAAIE6SfgVQMEgDPhAQ%3D%3D"
}
```

### ticket 换取二维码图片

获取二维码 ticket 后，开发者可用 ticket 换取二维码图片。

```php
$card->getQrCode($ticket);
```

> - `ticket` string> - 获取的二维码 ticket，凭借此 ticket 可以在有效时间内换取二维码。

示例：

```php
$ticket = 'gQFF8DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL01VTzN0T0hsS1BwUlBBYUszbVN5AAIEughxVwMEAKd2AA==';
$result = $card->getQrCode($ticket);
```

### ticket 换取二维码链接

```php
$card->getQrCodeUrl($ticket);
```

示例：

```php
$ticket = 'gQFF8DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL01VTzN0T0hsS1BwUlBBYUszbVN5AAIEughxVwMEAKd2AA==';
$card->getQrCodeUrl($ticket);
```

### 创建货架接口

开发者需调用该接口创建货架链接，用于卡券投放。创建货架时需填写投放路径的场景字段。

```php
$card->createLandingPage($banner, $pageTitle, $canShare, $scene, $cards);
```

> - `banner` string -页面的 banner 图;
> - `pageTitle` string - 页面的 title
> - `canShare` bool - 页面是不是可以分享，true 或 false
> - `scene` string - 投放页面的场景值，具体值请参考下面的 example
> - `cards` array - 卡券列表，每个元素有两个字段

示例：

```php
$banner = 'http://mmbiz.qpic.cn/mmbiz/iaL1LJM1mF9aRKPZJkmG8xXhiaHqkKSVMMWeN3hLut7X7hicFN';
$pageTitle = '惠城优惠大派送';
$canShare  = true;

//SCENE_NEAR_BY          附近
//SCENE_MENU             自定义菜单
//SCENE_QRCODE             二维码
//SCENE_ARTICLE             公众号文章
//SCENE_H5                 h5页面
//SCENE_IVR                 自动回复
//SCENE_CARD_CUSTOM_CELL 卡券自定义cell
$scene = 'SCENE_NEAR_BY';

$cardList = [
    ['card_id' => 'pdkJ9uLRSbnB3UFEjZAgUxAJrjeY', 'thumb_url' => 'http://test.digilinx.cn/wxApi/Uploads/test.png'],
    ['card_id' => 'pdkJ9uJ37aU-tyRj4_grs8S45k1c', 'thumb_url' => 'http://test.digilinx.cn/wxApi/Uploads/aa.jpg'],
];

$result = $card->createLandingPage($banner, $pageTitle, $canShare, $scene, $cardList);
```

### 图文消息群发卡券

> 特别注意：目前该接口仅支持填入非自定义 code 的卡券,自定义 code 的卡券需先进行 code 导入后调用。

```php
$card->getHtml($cardId);
```

示例：

```php
$cardId = 'pdkJ9uLCEF_HSKO7JdQOUcZ-PUzo';

$result = $card->getHtml($cardId);
```

### 设置测试白名单

同时支持“openid”、“username”两种字段设置白名单，总数上限为 10 个。

```php
$card->setTestWhitelist($openids); // 使用 openid
$card->setTestWhitelistByName($usernames); // 使用 username
```

> - `openids` array - 测试的 openid 列表
> - `usernames` array> - 测试的微信号列表

示例：

```php
// by openid
$openids   = [$openId, $openId2, $openid3...];
$result = $card->setTestWhitelist($openids);

// by username
$usernames = ['tianye0327', 'iovertrue'];
$result = $card->setTestWhitelistByName($usernames);
```

### 获取用户已领取卡券接口

用于获取用户卡包里的，属于该 appid 下所有**可用卡券，包括正常状态和未生效状态**。

```php
$card->getUserCards($openid, $cardId);
```

示例：

```php
$openid  = 'odkJ9uDUz26RY-7DN1mxkznfo9xU';
$cardId = ''; // 卡券ID。不填写时默认查询当前 appid 下的卡券。

$result = $card->getUserCards($openid, $cardId);
```

### 设置微信买单接口

```php
$card->setPayCell($cardId, $isOpen = true);
```

> - `isOpen` string - 是否开启买单功能，填 true/false，不填默认 true

示例：

```php
$cardId = 'pdkJ9uH7u11R-Tu1kilbaW_zDFow';

$result = $card->setPayCell($cardId); // isOpen = true
$result = $card->setPayCell($cardId, $isOpen);
```

### 修改库存接口

```php
$card->increaseStock($cardId, $amount); // 增加库存
$card->reductStock($cardId, $amount); // 减少库存
```

> - `cardId` string - 卡券 ID
> - `amount` int - 修改多少库存

示例：

```php
$cardId = 'pdkJ9uLRSbnB3UFEjZAgUxAJrjeY';

$result = $card->increaseStock($cardId, 100);
```

## 卡券 Code

### 导入 code 接口

在自定义 code 卡券成功创建并且通过审核后，必须将自定义 code 按照与发券方的约定数量调用导入 code 接口导入微信后台。

```php
$card->code->deposit($cardId, $codes);
```

> - `cardId` string - 要导入 code 的卡券 ID
> - `codes` array - 要导入微信卡券后台的自定义 code，最多 100 个

示例：

```php
$cardId = 'pdkJ9uLCEF_HSKO7JdQOUcZ-PUzo';
$codes    = ['11111', '22222', '33333'];

$result = $card->code->deposit($cardId, $codes);
```

### 查询导入 code 数目

```php
$card->code->getDepositedCount($cardId);  // 要导入 code 的卡券 ID
```

示例：

```php
$cardId = 'pdkJ9uLCEF_HSKO7JdQOUcZ-PUzo';

$result = $card->code->getDepositedCount($cardId);
```

### 核查 code 接口

为了避免出现导入差错，强烈建议开发者在查询完 code 数目的时候核查 code 接口校验 code 导入微信后台的情况。

```php
$card->code->check($cardId, $codes);
```

示例：

```php
$cardId = 'pdkJ9uLCEF_HSKO7JdQOUcZ-PUzo';

$codes = ['807732265476', '22222', '33333'];

$result = $card->code->check($cardId, $codes);
```

### 查询 Code 接口

```php
$card->code->get($code, $cardId, $checkConsume = true);
```

> - checkConsume 是否校验 code 核销状态，true 和 false

示例：

```php
$code = '736052543512';
$cardId = 'pdkJ9uDgnm0pKfrTb1yV0dFMO_Gk';

$result = $card->code->get($code, $cardId);
$result = $card->code->get($code, $cardId, false); // check_consume = false
```

### 核销 Code 接口

```php
$card->code->consume($code);
// 或者指定 cardId
$card->code->consume($code, $cardId);
```

示例：

```php
$code = '789248558333';
$cardId = 'pdkJ9uDmhkLj6l5bm3cq9iteQBck';

$result = $card->code->consume($code);
// 或
$result = $card->code->consume($code, $cardId);
```

### Code 解码接口

```php
$card->code->decrypt($encryptedCode);
```

示例：

```php
$encryptedCode = 'XXIzTtMqCxwOaawoE91+VJdsFmv7b8g0VZIZkqf4GWA60Fzpc8ksZ/5ZZ0DVkXdE';

$result = $card->code->decrypt($encryptedCode);
```

### 更改 Code 接口

```php
$card->code->update($code, $newCode, $cardId);
```

> - `newCode` string - 变更后的有效 Code 码

示例：

```php
$code = '148246271394';
$newCode = '659266965266';
$cardId = '';

$result = $card->code->update($code, $newCode, $cardId);
```

### 设置卡券失效

```php
$card->code->disable($code, $cardId);
```

示例：

```php
$code    = '736052543512';
$cardId = '';

$result = $card->code->disable($code, $cardId);
```

## 通用卡券

## 卡券激活

```php
$result = $card->general_card->activate($info);
```

## 撤销激活

```php
$result = $card->general_card->deactivate(string $cardId, string $code);
```

## 更新用户信息

```php
$result = $card->general_card->updateUser(array $info);
```

## 会员卡

### 会员卡激活

```php
$result = $card->member_card->activate($info);
```

> - `info` - 需要激活的会员卡信息

示例：

```php
$info = [
      'membership_number'        => '357898858', //会员卡编号，由开发者填入，作为序列号显示在用户的卡包里。可与Code码保持等值。
      'code'                     => '916679873278', //创建会员卡时获取的初始code。
      'activate_begin_time'      => '1397577600', //激活后的有效起始时间。若不填写默认以创建时的 data_info 为准。Unix时间戳格式
      'activate_end_time'        => '1422724261', //激活后的有效截至时间。若不填写默认以创建时的 data_info 为准。Unix时间戳格式。
      'init_bonus'               => '持白金会员卡到店消费，可享8折优惠。', //初始积分，不填为0。
      'init_balance'             => '持白金会员卡到店消费，可享8折优惠。', //初始余额，不填为0。
      'init_custom_field_value1' => '白银', //创建时字段custom_field1定义类型的初始值，限制为4个汉字，12字节。
      'init_custom_field_value2' => '9折', //创建时字段custom_field2定义类型的初始值，限制为4个汉字，12字节。
      'init_custom_field_value3' => '200', //创建时字段custom_field3定义类型的初始值，限制为4个汉字，12字节。
];

$result = $card->member_card->activate($info);
```

### 设置开卡字段

```php
$card->member_card->setActivationForm($cardId, $settings);
```

> - `settings` array - 会员卡激活时的选项

示例：

```php
$cardId = 'pdkJ9uJYAyfLXsUCwI2LdH2Pn1AU';

$settings = [
    'required_form' => [
        'common_field_id_list' => [
            'USER_FORM_INFO_FLAG_MOBILE',
            'USER_FORM_INFO_FLAG_LOCATION',
            'USER_FORM_INFO_FLAG_BIRTHDAY',
        ],
        'custom_field_list' => [
            '喜欢的食物',
        ],
    ],
    'optional_form' => [
        'common_field_id_list' => [
            'USER_FORM_INFO_FLAG_EMAIL',
        ],
        'custom_field_list' => [
            '喜欢的食物',
        ],
    ],
];

$result = $card->member_card->setActivationForm($cardId, $settings);
```

### 拉取会员信息

```php
$card->member_card->getUser($cardId, $code);
```

示例：

```php
$cardId = 'pbLatjtZ7v1BG_ZnTjbW85GYc_E8';
$code    = '916679873278';

$result = $card->member_card->getUser($cardId, $code);
```

### 更新会员信息

```php
$card->member_card->updateUser($info);
```

> - `info` array - 可以更新的会员信息

示例：

```php
$info = [
    'code'                => '916679873278', //卡券Code码。
    'card_id'             => 'pbLatjtZ7v1BG_ZnTjbW85GYc_E8', //卡券ID。
    'record_bonus'        => '消费30元，获得3积分', //商家自定义积分消耗记录，不超过14个汉字。
    'bonus'               => '100', //需要设置的积分全量值，传入的数值会直接显示，如果同时传入add_bonus和bonus,则前者无效。
    'balance'             => '持白金会员卡到店消费，可享8折优惠。', //需要设置的余额全量值，传入的数值会直接显示，如果同时传入add_balance和balance,则前者无效。
    'record_balance'      => '持白金会员卡到店消费，可享8折优惠。', //商家自定义金额消耗记录，不超过14个汉字。
    'custom_field_value1' => '100', //创建时字段custom_field1定义类型的最新数值，限制为4个汉字，12字节。
    'custom_field_value2' => '200', //创建时字段custom_field2定义类型的最新数值，限制为4个汉字，12字节。
    'custom_field_value3' => '300', //创建时字段custom_field3定义类型的最新数值，限制为4个汉字，12字节。
];

$result = $card->member_card->updateUser($info);
```

## 子商户

### 添加子商户

```php
$card->sub_merchant->create(array $attributes); 
```

示例：

```php
$attributes = [
    'brand_name' => 'overtrue',
    'logo_url' => 'http://mmbiz.qpic.cn/mmbiz/iaL1LJM1mF9aRKPZJkmG8xXhiaHqkKSVMMWeN3hLut7X7hicFNjakmxibMLGWpXrEXB33367o7zHN0CwngnQY7zb7g/0',
    'protocol' => 'qIqwTfzAdJ_1-VJFT0fIV53DSY4sZY2WyhkzZzbV498Qgdp-K5HJtZihbHLS0Ys0',
    'end_time' => '1438990559',
    'primary_category_id' => 1,
    'secondary_category_id' => 101,
    'agreement_media_id' => '',
    'operator_media_id' => '',
    'app_id' => '',
];

$result = $card->sub_merchant->create($attributes);
```

### 更新子商户

```php
$card->sub_merchant->update(int $merchantId, array $info);
```

> - `$merchantId` int - 子商户 ID
> - `$info` array - 参数与创建子商户参数一样

示例：

```php
$info = [
  //...
];
$result = $card->sub_merchant->update('12', $info);
```

## 特殊票券

### 机票值机

```php
$card->boarding_pass->checkin(array $params);
```

### 更新会议门票 - 更新用户

```php
$card->meeting_ticket->updateUser(array $params);
```

### 更新电影门票 - 更新用户

```php
$card->movie_ticket->updateUser(array $params);
```

## JSAPI

### 卡券批量下发到用户

```php
$cards = [
    ['card_id' => 'pdkJ9uLRSbnB3UFEjZAgUxAJrjeY', 'outer_id' => 2],
    ['card_id' => 'pdkJ9uJ37aU-tyRj4_grs8S45k1c', 'outer_id' => 3],
];
$json = $card->jssdk->assign($cards); // 返回 json 格式
```

返回 json，在模板里的用法：

```html
wx.addCard({ cardList:
<?= $json ?>, // 需要打开的卡券列表 success: function (res) { var cardList = res.cardList; // 添加的卡券列表信息 } });
```

### 获取 Ticket

```php
$card->jssdk->getTicket();
// 强制刷新
$card->jssdk->getTicket(true);
```
