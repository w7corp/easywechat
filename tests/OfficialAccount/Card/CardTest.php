<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Card;

use EasyWeChat\OfficialAccount\Card\Card;
use EasyWeChat\Foundation\Core\Http;
use EasyWeChat\Support\Arr;
use EasyWeChat\Tests\TestCase;

class CardTest extends TestCase
{
    /**
     * @return \Mockery\MockInterface|Card
     */
    public function getCard()
    {
        $card = \Mockery::mock('EasyWeChat\OfficialAccount\Card\Card[parseJSON]', [$this->getMockAccessToken()]);
        $card->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return [
                'api' => $params[0],
                'params' => empty($params[1]) ? null : $params[1],
            ];
        });

        return $card;
    }

    public function getMockCache()
    {
        return \Mockery::mock('Doctrine\Common\Cache\Cache');
    }

    public function getMockHttp()
    {
        $http = \Mockery::mock(Http::class.'[get]', function ($mock) {
            $mock->shouldReceive('get')->andReturn(json_encode([
                'access_token' => 'thisIsATokenFromHttp',
                'expires_in' => 7200,
            ]));
        });

        return $http;
    }

    public function getMockAccessToken()
    {
        $accessToken = \Mockery::mock('EasyWeChat\OfficialAccount\Core\AccessToken[getTokenFromServer]', ['foo', 'bar']);
        $accessToken->shouldReceive('getTokenFromServer')->andReturn([
            'access_token' => 'foobar',
            'expires_in' => 7200,
        ]);

        return $accessToken;
    }

    //获取卡券颜色
    public function testGetColors()
    {
        $card = $this->getCard();

        $result = $card->getColors();

        $this->assertStringStartsWith(Card::API_GET_COLORS, $result['api']);
    }

    //创建卡券
    public function testCreate()
    {
        $card = $this->getCard();

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

        $type = strtolower($cardType);

        $result = $card->create($cardType, $baseInfo, $especial);
        $this->assertStringStartsWith(Card::API_CREATE_CARD, $result['api']);
        $this->assertEquals($cardType, $result['params']['card']['card_type']);
        $this->assertEquals($baseInfo, $result['params']['card'][$type]['base_info']);
    }

    //创建二维码
    public function testQRCode()
    {
        $card = $this->getCard();

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

        $result = $card->QRCode($cards);
        $this->assertStringStartsWith(Card::API_CREATE_QRCODE, $result['api']);
        $this->assertEquals($cards, $result['params']);
    }

    //ticket 换取二维码图片
    public function testShowQRCode()
    {
        $card = $this->getCard();

        $ticket = 'gQFF8DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL01VTzN0T0hsS1BwUlBBYUszbVN5AAIEughxVwMEAKd2AA==';
        // $card->showQRCode($ticket);
    }

    //通过ticket换取二维码 链接
    public function testGetQRCodeUrl()
    {
        $card = $this->getCard();

        $ticket = 'gQFF8DoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL01VTzN0T0hsS1BwUlBBYUszbVN5AAIEughxVwMEAKd2AA==';
        $card->getQRCodeUrl($ticket);
    }

    //获取 卡券 Api_ticket
    public function testGetAPITicket()
    {
        $http = $this->getMockHttp();
        $cache = $this->getMockCache();

        $cache->shouldReceive('fetch')->andReturn('foo');
        $accessToken = $this->getMockAccessToken();
        $card = new Card($accessToken);
        $card->setCache($cache);
        $card->setHttp($http);
        $card->setUrl('http://easywechat.org');

        $cache->shouldReceive('save')->andReturn('foo');
        $this->assertNull($card->getAPITicket(true));
    }

    //微信卡券：JSAPI 卡券Package - 基础参数没有附带任何值 - 再生产环境中需要根据实际情况进行修改
    public function testWxCardPackage()
    {
        $cardList = [
            ['card_id' => 'pdkJ9uLRSbnB3UFEjZAgUxAJrjeY', 'outer_id' => 2],
            ['card_id' => 'pdkJ9uJ37aU-tyRj4_grs8S45k1c', 'outer_id' => 3],
        ];

        $http = $this->getMockHttp();
        $cache = $this->getMockCache();
        $cache->shouldReceive('fetch')->andReturnUsing(function ($key) {
            return 'overtrue.ticket';
        });
        $cache->shouldReceive('set')->andReturnUsing(function ($key, $ticket, $expires) {
            return $ticket;
        });

        $http->shouldReceive('get')->andReturn(['ticket' => 'overtrue.ticket', 'expires_in' => 7200]);
        $accessToken = $this->getMockAccessToken();
        $card = new Card($accessToken);
        $card->setCache($cache);
        $card->setHttp($http);

        $cache->shouldReceive('save')->andReturn('foo');
        $card->jsConfigForAssign($cardList);
    }

    //创建货架接口
    public function testCreateLandingPage()
    {
        $card = $this->getCard();

        $banner = 'http://mmbiz.qpic.cn/mmbiz/iaL1LJM1mF9aRKPZJkmG8xXhiaHqkKSVMMWeN3hLut7X7hicFN';
        $pageTitle = '惠城优惠大派送';
        $canShare = true;

        /** @var string $scene SCENE_NEAR_BY  附近, SCENE_MENU 自定义菜单, SCENE_QRCODE 二维码, SCENE_ARTICLE 公众号文章, SCENE_H5 h5页面, SCENE_IVR 自动回复, SCENE_CARD_CUSTOM_CELL 卡券自定义cell */
        $scene = 'SCENE_NEAR_BY';

        $cardList = [
            ['card_id' => 'pdkJ9uLRSbnB3UFEjZAgUxAJrjeY', 'thumb_url' => 'http://test.digilinx.cn/wxApi/Uploads/test.png'],
            ['card_id' => 'pdkJ9uJ37aU-tyRj4_grs8S45k1c', 'thumb_url' => 'http://test.digilinx.cn/wxApi/Uploads/aa.jpg'],
        ];

        $result = $card->createLandingPage($banner, $pageTitle, $canShare, $scene, $cardList);
        $this->assertStringStartsWith(Card::API_CREATE_LANDING_PAGE, $result['api']);
        $this->assertEquals($banner, $result['params']['banner']);
        $this->assertEquals($pageTitle, $result['params']['page_title']);
        $this->assertEquals($canShare, $result['params']['can_share']);
        $this->assertEquals($scene, $result['params']['scene']);
        $this->assertEquals($cardList, $result['params']['card_list']);
    }

    //导入code接口
    public function testDeposit()
    {
        $card = $this->getCard();

        $cardId = 'pdkJ9uLCEF_HSKO7JdQOUcZ-PUzo';
        $code = ['11111', '22222', '33333'];

        $result = $card->deposit($cardId, $code);
        $this->assertStringStartsWith(Card::API_DEPOSIT_CODE, $result['api']);
        $this->assertEquals($cardId, $result['params']['card_id']);
        $this->assertEquals($code, $result['params']['code']);
    }

    //查询导入code数目
    public function testGetDepositedCount()
    {
        $card = $this->getCard();

        $cardId = 'pdkJ9uLCEF_HSKO7JdQOUcZ-PUzo';

        $result = $card->getDepositedCount($cardId);
        $this->assertStringStartsWith(Card::API_GET_DEPOSIT_COUNT, $result['api']);
        $this->assertEquals($cardId, $result['params']['card_id']);
    }

    //核查code接口
    public function testCheckCode()
    {
        $card = $this->getCard();

        $cardId = 'pdkJ9uLCEF_HSKO7JdQOUcZ-PUzo';
        $code = ['807732265476', '22222', '33333'];

        $result = $card->checkCode($cardId, $code);
        $this->assertStringStartsWith(Card::API_CHECK_CODE, $result['api']);
        $this->assertEquals($cardId, $result['params']['card_id']);
        $this->assertEquals($code, $result['params']['code']);
    }

    //图文消息群发卡券
    public function testGetHtml()
    {
        $card = $this->getCard();

        $cardId = 'pdkJ9uLCEF_HSKO7JdQOUcZ-PUzo';

        $result = $card->getHtml($cardId);
        $this->assertStringStartsWith(Card::API_GET_HTML, $result['api']);
        $this->assertEquals($cardId, $result['params']['card_id']);
    }

    //设置测试白名单
    public function testSetTestWhitelist()
    {
        $card = $this->getCard();

        $openids = ['foo', 'bar', 'baz'];
        $usernames = ['tianye0327', 'iovertrue'];

        // openids
        $result = $card->setTestWhitelist($openids);
        $this->assertStringStartsWith(Card::API_SET_TEST_WHITE_LIST, $result['api']);
        $this->assertEquals($openids, $result['params']['openid']);
        // usernames
        $result = $card->setTestWhitelistByUsername($usernames);
        $this->assertStringStartsWith(Card::API_SET_TEST_WHITE_LIST, $result['api']);
        $this->assertEquals($usernames, $result['params']['username']);
    }

    //查询Code接口
    public function testGetCode()
    {
        $card = $this->getCard();

        $code = '736052543512';
        $checkConsume = true;
        $cardId = 'pdkJ9uDgnm0pKfrTb1yV0dFMO_Gk';

        $result = $card->getCode($code, $checkConsume, $cardId);
        $this->assertStringStartsWith(Card::API_GET_CODE, $result['api']);
        $this->assertEquals($code, $result['params']['code']);
        $this->assertEquals($checkConsume, $result['params']['check_consume']);
        $this->assertEquals($cardId, $result['params']['card_id']);
    }

    //核销Code接口
    public function testConsume()
    {
        $card = $this->getCard();

        $cardId = 'pdkJ9uDmhkLj6l5bm3cq9iteQBck';
        $code = '789248558333';

        $result = $card->consume($cardId, $code);
        $this->assertStringStartsWith(Card::API_CONSUME_CARD, $result['api']);
        $this->assertEquals($cardId, $result['params']['card_id']);
        $this->assertEquals($code, $result['params']['code']);

        // test cardId
        $result = $card->consume($code);
        $this->assertStringStartsWith(Card::API_CONSUME_CARD, $result['api']);
        $this->assertEquals($code, $result['params']['code']);
        $this->assertArrayNotHasKey('card_id', $result['params']);

        $result = $card->consume($cardId, $code);
        $this->assertStringStartsWith(Card::API_CONSUME_CARD, $result['api']);
        $this->assertEquals($code, $result['params']['code']);
        $this->assertEquals($cardId, $result['params']['card_id']);

        $result = $card->consume($code, $cardId);
        $this->assertStringStartsWith(Card::API_CONSUME_CARD, $result['api']);
        $this->assertEquals($code, $result['params']['code']);
        $this->assertEquals($cardId, $result['params']['card_id']);
    }

    //Code解码接口
    public function testDecryptCode()
    {
        $card = $this->getCard();

        $encryptedCode = 'XXIzTtMqCxwOaawoE91+VJdsFmv7b8g0VZIZkqf4GWA60Fzpc8ksZ/5ZZ0DVkXdE';

        $result = $card->decryptCode($encryptedCode);
        $this->assertStringStartsWith(Card::API_DECRYPT_CODE, $result['api']);
        $this->assertEquals($encryptedCode, $result['params']['encrypt_code']);
    }

    //获取用户已领取卡券接口
    public function testGetUserCards()
    {
        $card = $this->getCard();

        $openid = 'odkJ9uDUz26RY-7DN1mxkznfo9xU';
        $cardId = ''; //卡券ID。不填写时默认查询当前appid下的卡券。

        $result = $card->getUserCards($openid, $cardId);
        $this->assertStringStartsWith(Card::API_GET_CARD_LIST, $result['api']);
        $this->assertEquals($openid, $result['params']['openid']);
        $this->assertEquals($cardId, $result['params']['card_id']);
    }

    //查看卡券详情
    public function testGetCard()
    {
        $card = $this->getCard();

        $cardId = 'pdkJ9uLRSbnB3UFEjZAgUxAJrjeY';
        $result = $card->getCard($cardId);

        $this->assertStringStartsWith(Card::API_GET_CARD, $result['api']);
        $this->assertEquals($cardId, $result['params']['card_id']);
    }

    //批量查询卡列表
    public function testLists()
    {
        $card = $this->getCard();

        $offset = 0;
        $count = 10;
        $statusList = 'CARD_STATUS_VERIFY_OK';

        $result = $card->lists($offset, $count, $statusList);
        $this->assertStringStartsWith(Card::API_LIST_CARD, $result['api']);
        $this->assertEquals($offset, $result['params']['offset']);
        $this->assertEquals($count, $result['params']['count']);
        $this->assertEquals($statusList, $result['params']['status_list']);
    }

    //更改卡券信息接口 and 设置跟随推荐接口
    public function testUpdate()
    {
        $card = $this->getCard();

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
        $this->assertStringStartsWith(Card::API_UPDATE_CARD, $result['api']);
        $this->assertEquals($cardId, $result['params']['card_id']);
        $this->assertEquals($baseInfo, $result['params'][$type]['base_info']);
    }

    //设置微信买单接口
    public function testSetPayCell()
    {
        $card = $this->getCard();

        $cardId = 'pdkJ9uH7u11R-Tu1kilbaW_zDFow';
        $isOpen = true;

        $result = $card->setPayCell($cardId, $isOpen);
        $this->assertStringStartsWith(Card::API_SET_PAY_CELL, $result['api']);
        $this->assertEquals($cardId, $result['params']['card_id']);
        $this->assertEquals($isOpen, $result['params']['is_open']);
    }

    //修改库存接口
    public function testModifyStock()
    {
        $card = $this->getCard();

        $cardId = 'pdkJ9uLRSbnB3UFEjZAgUxAJrjeY';
        $result = $card->increaseStock($cardId, 100);

        $this->assertStringStartsWith(Card::API_MODIFY_STOCK, $result['api']);
        $this->assertEquals($cardId, $result['params']['card_id']);
        $this->assertEquals(100, $result['params']['increase_stock_value']);
        $result = $card->reduceStock($cardId, 100);
        $this->assertStringStartsWith(Card::API_MODIFY_STOCK, $result['api']);
        $this->assertEquals($cardId, $result['params']['card_id']);
        $this->assertEquals(100, $result['params']['reduce_stock_value']);
    }

    //更改Code接口
    //为确保转赠后的安全性，微信允许自定义Code的商户对已下发的code进行更改。
    //注：为避免用户疑惑，建议仅在发生转赠行为后（发生转赠后，微信会通过事件推送的方式告知商户被转赠的卡券Code）对用户的Code进行更改。
    public function testUpdateCode()
    {
        $card = $this->getCard();

        $code = '148246271394';
        $newCode = '659266965266';
        $cardId = '';

        $result = $card->updateCode($code, $newCode, $cardId);
        $this->assertStringStartsWith(Card::API_UPDATE_CODE, $result['api']);
        $this->assertEquals($code, $result['params']['code']);
        $this->assertEquals($newCode, $result['params']['new_code']);
        $this->assertEquals($cardId, $result['params']['card_id']);
    }

    //删除卡券接口
    public function testDelete()
    {
        $card = $this->getCard();

        $cardId = 'pdkJ9uItT7iUpBp4GjZp8Cae0Vig';

        $result = $card->delete($cardId);
        $this->assertStringStartsWith(Card::API_DELETE_CARD, $result['api']);
        $this->assertEquals($cardId, $result['params']['card_id']);
    }

    //设置卡券失效
    public function testDisable()
    {
        $card = $this->getCard();

        $code = '736052543512';
        $cardId = '';

        $result = $card->disable($code, $cardId);
        $this->assertStringStartsWith(Card::API_DISABLE_CARD, $result['api']);
        $this->assertEquals($code, $result['params']['code']);
        $this->assertEquals($cardId, $result['params']['card_id']);
    }

    //会员卡接口激活
    public function testActivate()
    {
        $card = $this->getCard();

        $activate = [
            'membership_number' => '357898858', //会员卡编号，由开发者填入，作为序列号显示在用户的卡包里。可与Code码保持等值。
            'code' => '916679873278', //创建会员卡时获取的初始code。
            'activate_begin_time' => '1397577600', //激活后的有效起始时间。若不填写默认以创建时的 data_info 为准。Unix时间戳格式
            'activate_end_time' => '1422724261', //激活后的有效截至时间。若不填写默认以创建时的 data_info 为准。Unix时间戳格式。
            'init_bonus' => '持白金会员卡到店消费，可享8折优惠。', //初始积分，不填为0。
            'init_balance' => '持白金会员卡到店消费，可享8折优惠。', //初始余额，不填为0。
            'init_custom_field_value1' => '白银', //创建时字段custom_field1定义类型的初始值，限制为4个汉字，12字节。
            'init_custom_field_value2' => '9折', //创建时字段custom_field2定义类型的初始值，限制为4个汉字，12字节。
            'init_custom_field_value3' => '200', //创建时字段custom_field3定义类型的初始值，限制为4个汉字，12字节。
        ];

        $result = $card->activate($activate);
        $this->assertStringStartsWith(Card::API_ACTIVATE_MEMBER_CARD, $result['api']);
        $this->assertEquals($activate, $result['params']);
    }

    //设置开卡字段接口
    public function testActivateUserForm()
    {
        $card = $this->getCard();

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
        $this->assertStringStartsWith(Card::API_ACTIVATE_MEMBER_USER_FORM, $result['api']);
        $this->assertEquals($cardId, $result['params']['card_id']);
    }

    //拉取会员信息接口
    public function testGetMemberCardUser()
    {
        $card = $this->getCard();

        $cardId = 'pbLatjtZ7v1BG_ZnTjbW85GYc_E8';
        $code = '916679873278';

        $result = $card->getMemberCardUser($cardId, $code);
        $this->assertStringStartsWith(Card::API_GET_MEMBER_USER_INFO, $result['api']);
        $this->assertEquals($cardId, $result['params']['card_id']);
        $this->assertEquals($code, $result['params']['code']);
    }

    //更新会员信息
    public function testUpdateMemberCardUser()
    {
        $card = $this->getCard();

        $updateUser = [
            'code' => '916679873278', //卡券Code码。
            'card_id' => 'pbLatjtZ7v1BG_ZnTjbW85GYc_E8', //卡券ID。
            'record_bonus' => '消费30元，获得3积分', //商家自定义积分消耗记录，不超过14个汉字。
            'bonus' => '100', //需要设置的积分全量值，传入的数值会直接显示，如果同时传入add_bonus和bonus,则前者无效。
            'balance' => '持白金会员卡到店消费，可享8折优惠。', //需要设置的余额全量值，传入的数值会直接显示，如果同时传入add_balance和balance,则前者无效。
            'record_balance' => '持白金会员卡到店消费，可享8折优惠。', //商家自定义金额消耗记录，不超过14个汉字。
            'custom_field_value1' => '100', //创建时字段custom_field1定义类型的最新数值，限制为4个汉字，12字节。
            'custom_field_value2' => '200', //创建时字段custom_field2定义类型的最新数值，限制为4个汉字，12字节。
            'custom_field_value3' => '300', //创建时字段custom_field3定义类型的最新数值，限制为4个汉字，12字节。
        ];

        $result = $card->updateMemberCardUser($updateUser);
        $this->assertStringStartsWith(Card::API_UPDATE_MEMBER_CARD_USER, $result['api']);
        $this->assertEquals($updateUser, $result['params']);
    }

    //添加子商户
    public function testCreateSubMerchant()
    {
        $card = $this->getCard();

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
        $this->assertStringStartsWith(Card::API_CREATE_SUB_MERCHANT, $result['api']);
        $this->assertEquals($info, $result['params']['info']);
    }

    //添加子商户
    public function testUpdateSubMerchant()
    {
        $card = $this->getCard();

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

        $result = $card->updateSubMerchant('12', $info);
        $this->assertStringStartsWith(Card::API_UPDATE_SUB_MERCHANT, $result['api']);
        $this->assertEquals(12, $result['params']['info']['merchant_id']);
        $this->assertEquals($info, Arr::except($result['params']['info'], 'merchant_id'));
    }

    //卡券开放类目查询接口
    public function testGetCategories()
    {
        $card = $this->getCard();

        $result = $card->getCategories();
        $this->assertStringStartsWith(Card::API_GET_CATEGORIES, $result['api']);
    }
}
