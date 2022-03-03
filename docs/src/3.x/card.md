# 卡券
-

> Version `>=3.1.2`

## 获取实例

```php
<?php
use EasyWeChat\Foundation\Application;

// ...

$app = new Application($options);

$card = $app->card;
```


## API列表

### 获取卡券颜色

```php
$card->getColors();
```

example:

```php
$result = $card->getColors();
```



### 创建卡券

创建卡券接口是微信卡券的基础接口，用于创建一类新的卡券，获取card_id，创建成功并通过审核后，商家可以通过文档提供的其他接口将卡券下发给用户，每次成功领取，库存数量相应扣除。

```php
$card->create($cardType, $baseInfo, $especial);
```

- `cardType` string - 是要添加卡券的类型
- `baseInfo` array  - 为卡券的基本数据
- `especial` array  - 是扩展字段

example:

```php
<?php

	$cardType = 'GROUPON';

    $baseInfo = [
        'logo_url' => 'http://mmbiz.qpic.cn/mmbiz/2aJY6aCPatSeibYAyy7yct9zJXL9WsNVL4JdkTbBr184gNWS6nibcA75Hia9CqxicsqjYiaw2xuxYZiaibkmORS2oovdg/0',
        'brand_name' => '测试商户造梦空间',
        'code_type' => 'CODE_TYPE_QRCODE',
        'title' => '测试',
        'sub_title' => '测试副标题',
        'color' => 'Color010',
        'notice' => '测试使用时请出示此券',
        'service_phone' => '15311931577',
        'description' => "测试不可与其他优惠同享\n如需团购券发票，请在消费时向商户提出\n店内均可使用，仅限堂食",

        'date_info' => [
          'type' => 'DATE_TYPE_FIX_TERM',
          'fixed_term' => 90, //表示自领取后多少天内有效，不支持填写0
          'fixed_begin_term' => 0, //表示自领取后多少天开始生效，领取后当天生效填写0。
        ],

        'sku' => [
          'quantity' => '0', //自定义code时设置库存为0
        ],

        'location_id_list' => ['461907340'],  //获取门店位置poi_id，具备线下门店的商户为必填

        'get_limit' => 1,
        'use_custom_code' => true, //自定义code时必须为true
        'get_custom_code_mode' => 'GET_CUSTOM_CODE_MODE_DEPOSIT',  //自定义code时设置
        'bind_openid' => false,
        'can_share' => true,
        'can_give_friend' => false,
        'center_title' => '顶部居中按钮',
        'center_sub_title' => '按钮下方的wording',
        'center_url' => 'http://www.qq.com',
        'custom_url_name' => '立即使用',
        'custom_url' => 'http://www.qq.com',
        'custom_url_sub_title' => '6个汉字tips',
        'promotion_url_name' => '更多优惠',
        'promotion_url' => 'http://www.qq.com',
        'source' => '造梦空间',
      ];

    $especial = [
      'deal_detail' => 'deal_detail',
    ];

    $result = $card->create($cardType, $baseInfo, $especial);
```



### 创建二维码

开发者可调用该接口生成一张卡券二维码供用户扫码后添加卡券到卡包。

自定义Code码的卡券调用接口时，POST数据中需指定code，非自定义code不需指定，指定openid同理。指定后的二维码只能被用户扫描领取一次。

```php
$card->QRCode($cards);
```

- `cards` array - 卡券相关信息

example:

```php
//领取单张卡券
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

$result = $card->QRCode($cards);
```

```php
//领取多张卡券
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

$result = $card->QRCode($cardList);
```

请求成功返回值示例：

```php
array(4) {
  ["ticket"]=>
  string(96) "gQHa7joAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xLzdrUFlQMHJsV3Zvanc5a2NzV1N5AAIEJUVyVwMEAKd2AA=="
  ["expire_seconds"]=>
  int(7776000)
  ["url"]=>
  string(43) "http://weixin.qq.com/q/7kPYP0rlWvojw9kcsWSy"
  ["show_qrcode_url"]=>
  string(151) "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=gQHa7joAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xLzdrUFlQMHJsV3Zvanc5a2NzV1N5AAIEJUVyVwMEAKd2AA%3D%3D"
}
```

成功返回值列表说明：

|       参数名       | 描述                                       |
| :-------------: | :--------------------------------------- |
|     ticket      | 获取的二维码ticket，凭借此ticket调用[通过ticket换取二维码接口](http://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1443433542&token=&lang=zh_CN)可以在有效时间内换取二维码。 |
| expire_seconds  | 二维码的有效时间                                 |
|       url       | 二维码图片解析后的地址，开发者可根据该地址自行生成需要的二维码图片        |
| show_qrcode_url | 二维码显示地址，点击后跳转二维码页面                       |



### ticket 换取二维码图片

获取二维码 ticket 后，开发者可用 ticket 换取二维码图片。

```php
$card->showQRCode($ticket);
```

- `ticket` string  - 获取的二维码 ticket，凭借此 ticket 可以在有效时间内换取二维码。

example:

```php
$ticket = 'gQFF8DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL01VTzN0T0hsS1BwUlBBYUszbVN5AAIEughxVwMEAKd2AA==';
$result = $card->showQRCode($ticket);
```


### ticket 换取二维码链接

```php
$card->getQRCodeUrl($ticket);  //获取的二维码ticket
```

example:

```php
$ticket = 'gQFF8DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL01VTzN0T0hsS1BwUlBBYUszbVN5AAIEughxVwMEAKd2AA==';
$card->getQRCodeUrl($ticket);
```

### JSAPI 卡券批量下发到用户

微信卡券：JSAPI 卡券

```php
$cards = [
    ['card_id' => 'pdkJ9uLRSbnB3UFEjZAgUxAJrjeY', 'outer_id' => 2],
    ['card_id' => 'pdkJ9uJ37aU-tyRj4_grs8S45k1c', 'outer_id' => 3],
];
$json = $card->jsConfigForAssign($cards); // 返回 json 格式
```

返回 json，在模板里的用法：

```html
wx.addCard({
    cardList: <?= $json ?>, // 需要打开的卡券列表
    success: function (res) {
        var cardList = res.cardList; // 添加的卡券列表信息
    }
});
```

### 创建货架接口

开发者需调用该接口创建货架链接，用于卡券投放。创建货架时需填写投放路径的场景字段。

```php
$card->createLandingPage($banner, $pageTitle, $canShare, $scene, $cards);
```

- `banner` string -页面的 banner 图;
- `pageTitle` string - 页面的 title
- `canShare` bool - 页面是不是可以分享，true 或 false
- `scene`  string - 投放页面的场景值，具体值请参考下面的 example
- `cards`  array - 卡券列表，每个元素有两个字段

example:

```php
$banner     = 'http://mmbiz.qpic.cn/mmbiz/iaL1LJM1mF9aRKPZJkmG8xXhiaHqkKSVMMWeN3hLut7X7hicFN';
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



### 导入code接口

在自定义code卡券成功创建并且通过审核后，必须将自定义code按照与发券方的约定数量调用导入code接口导入微信后台。

```php
$card->deposit($card_id, $code);
```

- `cardId` string - 要导入code的卡券ID
- `code` string - 要导入微信卡券后台的自定义 code，最多100个

example:

```php
$cardId = 'pdkJ9uLCEF_HSKO7JdQOUcZ-PUzo';
$code    = ['11111', '22222', '33333'];

$result = $card->deposit($cardId, $code);
```



### 查询导入code数目

```php
$card->getDepositedCount($cardId);  //要导入code的卡券ID
```

example:

```php
$cardId = 'pdkJ9uLCEF_HSKO7JdQOUcZ-PUzo';

$result = $card->getDepositedCount($cardId);
```



### 核查code接口

为了避免出现导入差错，强烈建议开发者在查询完code数目的时候核查code接口校验code导入微信后台的情况。

```php
$card->checkCode($cardId, $code);
```

example:

```php
$cardId = 'pdkJ9uLCEF_HSKO7JdQOUcZ-PUzo';

$code = ['807732265476', '22222', '33333'];

$result = $card->checkCode($cardId, $code);
```



### 图文消息群发卡券

特别注意：目前该接口仅支持填入非自定义code的卡券,自定义code的卡券需先进行code导入后调用。

```php
$card->getHtml($cardId);
```

example:

```php
$cardId = 'pdkJ9uLCEF_HSKO7JdQOUcZ-PUzo';

$result = $card->getHtml($cardId);
```



### 设置测试白名单

同时支持“openid”、“username”两种字段设置白名单，总数上限为10个。

```php
$card->setTestWhitelist($openids); // 使用 openid
$card->setTestWhitelistByUsername($usernames); // 使用 username
```

- `openids` array - 测试的openid列表
- `usernames` array  - 测试的微信号列表

example:

```php
// by openid
$openids   = [$openId, $openId2, $openid3...];
$result = $card->setTestWhitelist($openids);

// by username
$usernames = ['tianye0327', 'iovertrue'];
$result = $card->setTestWhitelistByUsername($usernames);
```

### 查询Code接口

```php
$card->getCode($code, $checkConsume, $cardId);
```

- checkConsume  是否校验code核销状态，true和false

example:

```php
$code          = '736052543512';
$checkConsume = true;
$cardId       = 'pdkJ9uDgnm0pKfrTb1yV0dFMO_Gk';

$result = $card->getCode($code, $checkConsume, $cardId);
```



### 核销Code接口

```php
$card->consume($code);

// 或者指定 cardId

$card->consume($code, $cardId);
```

example:

```php
$cardId = 'pdkJ9uDmhkLj6l5bm3cq9iteQBck';
$code    = '789248558333';

$result = $card->consume($code);

//或

$result = $card->consume($code, $cardId);
```



### Code解码接口

```php
$card->decryptCode($encryptedCode);
```

example:

```php
$encryptedCode = 'XXIzTtMqCxwOaawoE91+VJdsFmv7b8g0VZIZkqf4GWA60Fzpc8ksZ/5ZZ0DVkXdE';

$result = $card->decryptCode($encryptedCode);
```



### 获取用户已领取卡券接口

用于获取用户卡包里的，属于该appid下所有**可用卡券，包括正常状态和未生效状态**。

```php
$card->getUserCards($openid, $cardId);
```

example:

```php
$openid  = 'odkJ9uDUz26RY-7DN1mxkznfo9xU';
$cardId = ''; //卡券ID。不填写时默认查询当前appid下的卡券。

$result = $card->getUserCards($openid, $cardId);
```



### 查看卡券详情

开发者可以调用该接口查询某个card_id的创建信息、审核状态以及库存数量。

```php
$card->getCard($cardId);
```

example:

```php
$cardId = 'pdkJ9uLRSbnB3UFEjZAgUxAJrjeY';

$result = $card->getCard($cardId);
```



### 批量查询卡列表

```php
$card->lists($offset, $count, $statusList);
```

- `offset` int - 查询卡列表的起始偏移量，从0开始
- `count` int - 需要查询的卡片的数量
- `statusList` -  支持开发者拉出指定状态的卡券列表，详见example

example:

```php
$offset      = 0;
$count       = 10;

//CARD_STATUS_NOT_VERIFY,待审核；
//CARD_STATUS_VERIFY_FAIL,审核失败；
//CARD_STATUS_VERIFY_OK，通过审核；
//CARD_STATUS_USER_DELETE，卡券被商户删除；
//CARD_STATUS_DISPATCH，在公众平台投放过的卡券；
$statusList = 'CARD_STATUS_VERIFY_OK';

$result = $card->lists($offset, $count, $statusList);
```



### 更改卡券信息接口

支持更新所有卡券类型的部分通用字段及特殊卡券中特定字段的信息。

```php
$card->update($cardId, $type, $baseInfo);
```

- `type` string - 卡券类型

example:

```php
$cardId = 'pdkJ9uCzKWebwgNjxosee0ZuO3Os';

$type = 'groupon';

$baseInfo = [
    'logo_url' => 'http://mmbiz.qpic.cn/mmbiz/2aJY6aCPatSeibYAyy7yct9zJXL9WsNVL4JdkTbBr184gNWS6nibcA75Hia9CqxicsqjYiaw2xuxYZiaibkmORS2oovdg/0',
    'center_title' => '顶部居中按钮',
    'center_sub_title' => '按钮下方的wording',
    'center_url' => 'http://www.baidu.com',
    'custom_url_name' => '立即使用',
    'custom_url' => 'http://www.qq.com',
    'custom_url_sub_title' => '6个汉字tips',
    'promotion_url_name' => '更多优惠',
    'promotion_url' => 'http://www.qq.com',
];

$result = $card->update($cardId, $type, $baseInfo);
```



### 设置微信买单接口

```php
$card->setPayCell($cardId, $isOpen);
```

- `isOpen` string - 是否开启买单功能，填 true/false，不填默认 true

example:

```php
$cardId = 'pdkJ9uH7u11R-Tu1kilbaW_zDFow';
$isOpen = true;

$result = $card->setPayCell($cardId, $isOpen);
```



### 修改库存接口

```php
$card->increaseStock($cardId, $amount); // 增加库存
$card->reductStock($cardId, $amount); // 减少库存
```

- `cardId` string - 卡券 ID
- `amount` int - 修改多少库存

example:

```php
$cardId = 'pdkJ9uLRSbnB3UFEjZAgUxAJrjeY';

$result = $card->increaseStock($cardId, 100);
```


### 更改Code接口

```php
$card->updateCode($code, $newCode, $cardId);
```

- `newCode` string - 变更后的有效Code码

example:

```php
$code     = '148246271394';
$newCode = '659266965266';
$cardId  = '';

$result = $card->updateCode($code, $newCode, $cardId);
```



### 删除卡券接口

```php
$card->delete($cardId);
```

example:

```php
$cardId = 'pdkJ9uItT7iUpBp4GjZp8Cae0Vig';

$result = $card->delete($cardId);
```



### 设置卡券失效

```php
$card->disable($code, $cardId);
```

example:

```php
$code    = '736052543512';
$cardId = '';

$result = $card->disable($code, $cardId);
```



### 会员卡接口激活

```php
$result = $card->activate($info);
```

- `info` - 需要激活的会员卡信息

example:

```php
$activate = [
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

$result = $card->activate($activate);
```



### 设置开卡字段接口

```php
$card->activateUserForm($cardId, $requiredForm, $optionalForm);
```

- `requiredForm` array - 会员卡激活时的必填选项
- `optionalForm` array - 会员卡激活时的选填项

example:

```php
$cardId = 'pdkJ9uJYAyfLXsUCwI2LdH2Pn1AU';

$requiredForm = [
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
];

$optionalForm = [
    'optional_form' => [
        'common_field_id_list' => [
            'USER_FORM_INFO_FLAG_EMAIL',
        ],
        'custom_field_list' => [
            '喜欢的食物',
        ],
    ],
];

$result = $card->activateUserForm($cardId, $requiredForm, $optionalForm);
```



### 拉取会员信息接口

```php
$card->getMemberCardUser($cardId, $code);
```

example:

```php
$cardId = 'pbLatjtZ7v1BG_ZnTjbW85GYc_E8';
$code    = '916679873278';

$result = $card->getMemberCardUser($cardId, $code);
```



### 更新会员信息

```php
$card->updateMemberCardUser($updateUser);
```

- `updateUser` array - 可以更新的会员信息

example:

```php
$updateUser = [
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

$result = $card->updateMemberCardUser($updateUser);
```



### 添加子商户

```php
$card->craeteSubMerchant($brandName, $logoUrl, $protocol, $endTime, $primaryCategoryId, $secondaryCategoryId, $agreementMediaId, $operatorMediaId, $appId); 
```

- `brand_name` string - 子商户名称（12个汉字内），该名称将在制券时填入并显示在卡券页面上
- `logo_url`  string - 子商户 logo，可通过上传 logo 接口获取。该 logo 将在制券时填入并显示在卡券页面上
- `protocol`  string - 授权函ID，即通过上传临时素材接口上传授权函后获得的 meida_id
- `primary_category_id`  int - 一级类目id,可以通过本文档中接口查询
- `secondary_category_id` int - 二级类目id，可以通过本文档中接口查询
- `agreement_media_id`  string - 营业执照或个体工商户营业执照彩照或扫描件
- `operator_media_id`  string - 营业执照内登记的经营者身份证彩照或扫描件
- `app_id`  string - 子商户的公众号 app_id，配置后子商户卡券券面上的 app_id 为该 app_id, app_id 须经过认证

example:

```php
$info = [
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

$result = $card->createSubMerchant($info);
```

### 更新子商户

```php
$card->updateSubMerchant($merchantId, $info);
```

- `$merchantId` int - 子商户 ID
- `$info` array - 参数与创建子商户参数一样

example:

```php
$info = [
  //...
];
$result = $card->updateSubMerchant('12', $info);
```

### 卡券开放类目查询接口

```php
$card->getCategories();
```

example:

```php
$result = $card->getCategories();
```

关于卡券接口的使用请参阅官方文档：http://mp.weixin.qq.com/wiki/
