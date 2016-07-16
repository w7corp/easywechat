<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Card.php.
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace Overtrue\Wechat;

use Overtrue\Wechat\Utils\Arr;
use Overtrue\Wechat\Utils\Bag;
use Overtrue\Wechat\Utils\JSON;

/**
 * 卡券.
 */
class Card
{
    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    /**
     * Cache对象
     *
     * @var Cache
     */
    protected $cache;

    /**
     * js ticket.
     *
     * @var string
     */
    protected $ticket;

    // 卡券类型
    const TYPE_GENERAL_COUPON = 'GENERAL_COUPON';   // 通用券
    const TYPE_GROUPON = 'GROUPON';          // 团购券
    const TYPE_DISCOUNT = 'DISCOUNT';         // 折扣券
    const TYPE_GIFT = 'GIFT';             // 礼品券
    const TYPE_CASH = 'CASH';             // 代金券
    const TYPE_MEMBER_CARD = 'MEMBER_CARD';      // 会员卡
    const TYPE_SCENIC_TICKET = 'SCENIC_TICKET';    // 景点门票
    const TYPE_MOVIE_TICKET = 'MOVIE_TICKET';     // 电影票
    const TYPE_BOARDING_PASS = 'BOARDING_PASS';    // 飞机票
    const TYPE_LUCKY_MONEY = 'LUCKY_MONEY';      // 红包
    const TYPE_MEETING_TICKET = 'MEETING_TICKET';   // 会议门票

    const CARD_STATUS_NOT_VERIFY = 'CARD_STATUS_NOT_VERIFY';   // 待审核
    const CARD_STATUS_VERIFY_FAIL = 'CARD_STATUS_VERIFY_FAIL';   //审核失败
    const CARD_STATUS_VERIFY_OK = 'CARD_STATUS_VERIFY_OK';     //通过审核
    const CARD_STATUS_USER_DELETE = 'CARD_STATUS_USER_DELETE';   //卡券被商户删除
    const CARD_STATUS_USER_DISPATCH = 'CARD_STATUS_USER_DISPATCH'; //在公众平台投放过的卡券 

    const API_CREATE = 'https://api.weixin.qq.com/card/create';
    const API_DELETE = 'https://api.weixin.qq.com/card/delete';
    const API_GET = 'https://api.weixin.qq.com/card/get';
    const API_UPDATE = 'https://api.weixin.qq.com/card/update';
    const API_LIST = 'https://api.weixin.qq.com/card/batchget';
    const API_CONSUME = 'https://api.weixin.qq.com/card/code/consume';
    const API_UNAVAILABLE = 'https://api.weixin.qq.com/card/code/unavailable';
    const API_CODE_GET = 'https://api.weixin.qq.com/card/code/get';
    const API_CODE_UPDATE = 'https://api.weixin.qq.com/card/code/update';
    const API_CODE_DECRYPT = 'https://api.weixin.qq.com/card/code/decrypt';
    const API_UPDATE_STOCK = 'https://api.weixin.qq.com/card/modifystock';
    const API_MEMBER_CARD_ACTIVE = 'https://api.weixin.qq.com/card/membercard/activate';
    const API_MEMBER_CARD_TRADE = 'https://api.weixin.qq.com/card/membercard/updateuser';
    const API_MOVIE_TICKET_UPDATE = 'https://api.weixin.qq.com/card/movieticket/updateuser';
    const API_BOARDING_PASS_CHECKIN = 'https://api.weixin.qq.com/card/boardingpass/checkin';
    const API_MEETING_TICKET_UPDATE = 'https://api.weixin.qq.com/card/meetingticket/updateuser';
    const API_TICKET = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=wx_card';
    const API_TESTWHITELIST = 'https://api.weixin.qq.com/card/testwhitelist/set';
    const API_USER_CARD_LIST = 'https://api.weixin.qq.com/card/user/getcardlist';
    const API_LANDINGPAGE_CREATE = 'https://api.weixin.qq.com/card/landingpage/create';

    /**
     * constructor.
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->http = new Http(new AccessToken($appId, $appSecret));
        $this->cache = new Cache($appId);
    }

    /**
     * 获取jsticket.
     *
     * @return string
     */
    public function getTicket()
    {
        if ($this->ticket) {
            return $this->ticket;
        }

        $key = 'overtrue.wechat.card.api_ticket';

        // for php 5.3
        $http = $this->http;
        $cache = $this->cache;
        $apiTicket = self::API_TICKET;

        return $this->ticket = $this->cache->get(
            $key,
            function ($key) use ($http, $cache, $apiTicket) {
                $result = $http->get($apiTicket);

                $cache->set($key, $result['ticket'], $result['expires_in']);

                return $result['ticket'];
            }
        );
    }

    /**
     * 生成 js添加到卡包 需要的 card_list 项.
     *
     * @param string $cardId
     * @param array  $extension
     *
     * @return string
     */
    public function attachExtension($cardId, array $extension = array())
    {
        $timestamp = time();

        $ext = array(
                'code' => Arr::get($extension, 'code'),
                'openid' => Arr::get($extension, 'openid', Arr::get($extension, 'open_id')),
                'timestamp' => $timestamp,
                'outer_id' => Arr::get($extension, 'outer_id'),
                'balance' => Arr::get($extension, 'balance'),
               );

        $ext['signature'] = $this->getSignature(
            $this->getTicket(),
            $timestamp,
            $cardId,
            $ext['code'],
            $ext['openid'],
            $ext['balance']
        );

        return array(
                'cardId' => $cardId,
                'cardExt' => JSON::encode($ext),
               );
    }

    /**
     * 创建卡券.
     *
     * @param array  $base
     * @param array  $properties
     * @param string $type
     *
     * @return string
     */
    public function create(array $base, array $properties = array(), $type = self::TYPE_GENERAL_COUPON)
    {
        $key = strtolower($type);
        $card = array_merge(array('base_info' => $base), $properties);
        $params = array(
                   'card' => array(
                              'card_type' => $type,
                              $key => $card,
                             ),
                  );

        $result = $this->http->jsonPost(self::API_CREATE, $params);

        return $result['card_id'];
    }

    /**
     * 卡券详情.
     *
     * @param string $cardId
     *
     * @return Bag
     */
    public function get($cardId)
    {
        $params = array('card_id' => $cardId);

        $result = $this->http->jsonPost(self::API_GET, $params);

        return new Bag($result['card']);
    }

    /**
     * 修改卡券.
     *
     * @param string $cardId
     * @param string $type
     * @param array  $base
     * @param array  $data
     *
     * @return bool
     */
    public function update($cardId, $type, array $base = array(), array $data = array())
    {
        $key = strtolower($type);
        $card = array_merge(array('base_info' => $base), $data);

        $params = array(
                   'card_id' => $cardId,
                   $key => $card,
                  );

        return $this->http->jsonPost(self::API_UPDATE, $params);
    }

    /**
     * 批量获取卡券列表.
     *
     * @param int   $offset
     * @param int   $count
     * @param array $statusList
     *
     * @return array
     */
    public function lists($offset = 0, $count = 10, $statusList = array())
    {
        $params = array(
                   'offset' => $offset,
                   'count' => $count,
                   'status_list' => $statusList,
                  );

        $result = $this->http->jsonPost(self::API_LIST, $params);

        return $result['card_id_list'];
    }

    /**
     * 核销
     *
     * @param string $code   要消耗序列号
     * @param string $cardId 卡券 ID。创建卡券时 use_custom_code 填写 true 时必填。
     *                       非自定义 code 不必填写。
     *
     * @return Bag
     */
    public function consume($code, $cardId = null)
    {
        $params = array(
                   'code' => $code,
                   'card_id' => $cardId,
                  );

        return new Bag($this->http->jsonPost(self::API_CONSUME, $params));
    }

    /**
     * 废弃卡券，失效.
     *
     * @param string $code
     * @param string $cardId
     *
     * @return bool
     */
    public function disable($code, $cardId = null)
    {
        $params = array(
                   'code' => $code,
                   'card_id' => $cardId,
                  );

        return $this->http->jsonPost(self::API_UNAVAILABLE, $params);
    }

    /**
     * 删除卡券.
     *
     * @param string $cardId
     *
     * @return bool
     */
    public function delete($cardId)
    {
        $params = array('card_id' => $cardId);

        return $this->http->jsonPost(self::API_DELETE, $params);
    }

    /**
     * 修改库存.
     *
     * @param string $cardId
     * @param int    $amount
     *
     * @return bool
     */
    public function updateStock($cardId, $amount)
    {
        if (!$amount) {
            return true;
        }

        $key = $amount > 0 ? 'increase_stock_value' : 'reduce_stock_value';

        $params = array(
                   'card_id' => $cardId,
                   $key => abs($amount),
                  );

        return $this->http->jsonPost(self::API_UPDATE_STOCK, $params);
    }

    /**
     * 增加库存.
     *
     * @param string $cardId
     * @param int    $amount
     *
     * @return bool
     */
    public function incStock($cardId, $amount)
    {
        return $this->updateStock($cardId, abs($amount));
    }

    /**
     * 减少库存.
     *
     * @param string $cardId
     * @param int    $amount
     *
     * @return bool
     */
    public function decStock($cardId, $amount)
    {
        return $this->updateStock($cardId, abs($amount) * -1);
    }

    /**
     * 查询Code.
     *
     * @param string $code
     * @param string $cardId
     *
     * @return Bag
     */
    public function getCode($code, $cardId = null)
    {
        $params = array(
                   'code' => $code,
                   'card_id' => $cardId,
                  );

        return new Bag($this->http->jsonPost(self::API_CODE_GET, $params));
    }

    /**
     * 修改code.
     *
     * @param string $code
     * @param string $newCode
     * @param string $cardId
     *
     * @return bool
     */
    public function updateCode($code, $newCode, $cardId)
    {
        $params = array(
                   'code' => $code,
                   'card_id' => $cardId,
                   'new_code' => $newCode,
                  );

        return $this->http->jsonPost(self::API_CODE_UPDATE, $params);
    }

    /**
     * code 解码
     *
     * @param string $encryptedCode
     *
     * @return string
     */
    public function getRealCode($encryptedCode)
    {
        $params = array('encrypt_code' => $encryptedCode);

        $result = $this->http->jsonPost(self::API_CODE_DECRYPT, $params);

        return $result['code'];
    }

    /**
     * 激活/绑定会员卡
     *
     * <pre>
     * $data:
     * {
     *      "init_bonus": 100,
     *      "init_balance": 200,
     *      "membership_number": "AAA00000001", "code": "12312313",
     *      "card_id": "xxxx_card_id"
     * }
     * </pre>
     *
     * @param string $cardId
     * @param array  $data
     *
     * @return bool
     */
    public function memberCardActivate($cardId, array $data)
    {
        $params = array_merge(array('card_id' => $cardId), $data);

        return $this->http->jsonPost(self::API_MEMBER_CARD_ACTIVE, $params);
    }

    /**
     * 获取用户已领取卡券接口.
     *
     * @param string $openid
     * @param string $cardId
     *
     * @return array
     */
    public function userCardLists($openid, $cardId = null)
    {
        $params = array(
            'openid' => $openid,
            'card_id' => $cardId,
        );

        return new Bag($this->http->jsonPost(self::API_USER_CARD_LIST, $params));
    }

    /**
     * 会员卡交易.
     *
     * <pre>
     * $data:
     * {
     *     "code": "12312313",
     *     "card_id":"p1Pj9jr90_SQRaVqYI239Ka1erkI",
     *     "record_bonus": "消费30元，获得3积分",
     *     "add_bonus": 3,
     *     "add_balance": -3000
     *     "record_balance": "购买焦糖玛琪朵一杯，扣除金额30元。"
     * }
     * </pre>
     *
     * @param string $cardId
     * @param array  $data
     *
     * @return Bag
     */
    public function memberCardTrade($cardId, array $data)
    {
        $params = array_merge(array('card_id' => $cardId), $data);

        return new Bag($this->http->jsonPost(self::API_MEMBER_CARD_TRADE, $params));
    }

    /**
     * 电影票更新座位.
     *
     * <pre>
     * $data:
     * {
     *     "code" : "277217129962",
     *     "card_id": "p1Pj9jr90_SQRaVqYI239Ka1erkI",
     *     "ticket_class": "4D",
     *     "show_time": 1408493192, "duration"：120, "screening_room": "5 号影厅",
     *     "seat_number": [ "5 排 14 号" , "5 排 15 号" ]
     * }
     * </pre>
     *
     * @param string $cardId
     * @param array  $data
     *
     * @return bool
     */
    public function updateMovieTicket($cardId, array $data)
    {
        $params = array_merge(array('card_id' => $cardId), $data);

        return $this->http->jsonPost(self::API_MOVIE_TICKET_UPDATE, $params);
    }

    /**
     * 会议门票更新.
     *
     * <pre>
     * $data:
     * {
     *     "code": "717523732898",
     *     "card_id": "pXch-jvdwkJjY7evUFV-sGsoMl7A",
     *     "zone" : "C 区",
     *     "entrance" : "东北门",
     *     "seat_number" : "2 排 15 号"
     * }
     * </pre>
     *
     * @param string $cardId
     * @param array  $data
     *
     * @return bool
     */
    public function updateMeetingTicket($cardId, array $data)
    {
        $params = array_merge(array('card_id' => $cardId), $data);

        return $this->http->jsonPost(self::API_MEETING_TICKET_UPDATE, $params);
    }

    /**
     * 在线值机.
     *
     * <pre>
     * $data:
     * {
     *     "code": "198374613512",
     *     "card_id":"p1Pj9jr90_SQRaVqYI239Ka1erkI",
     *     "passenger_name": "乘客姓名",
     *     "class": "舱等",
     *     "seat": "座位号",
     *     "etkt_bnr": "电子客票号", "qrcode_data": "二维码数据", "is_cancel ": false
     * }
     * </pre>
     *
     * @param string $cardId
     * @param array  $data
     *
     * @return bool
     */
    public function checkin($cardId, array $data)
    {
        $params = array_merge(array('card_id' => $cardId), $data);

        return $this->http->jsonPost(self::API_BOARDING_PASS_CHECKIN, $params);
    }

    /**
     * 生成签名.
     *
     * @return string
     */
    public function getSignature()
    {
        $params = func_get_args();

        sort($params, SORT_STRING);

        return sha1(implode($params));
    }

    /**
     * 获取随机字符串.
     *
     * @return string
     */
    public function getNonce()
    {
        return uniqid('pre_');
    }

    /**
     * 设置测试白名单.
     *
     * <pre>
     * $data:
     * {
     *     "openIds": {
     *          "openid1",
     *          "openid2",
     *          "openid3"...
     *     }
     *     "usernames": {
     *          "username1",
     *          "username2",
     *          "username3"...
     *     }
     * }
     * </pre>
     *
     * @param array $data
     *
     * @return mixed
     */
    public function setWhitelist(array $data)
    {
        $data = array_merge(array('openIds' => array(), 'usernames' => array()), $data);
        $params = array_merge(array('openid' => $data['openIds']), array('username' => $data['usernames']));

        return $this->http->jsonPost(self::API_TESTWHITELIST, $params);
    }

    /**
     * 通过openId设置测试白名单.
     *
     * $data:
     * {
     *     "openid1",
     *     "openid2",
     *     "openid3"...
     * }
     *
     * @param array $data
     *
     * @return mixed
     */
    public function setWhitelistByOpenId(array $data)
    {
        return $this->setWhitelist(array('openIds' => $data));
    }

    /**
     * 通过username设置测试白名单.
     *
     * $data:
     * {
     *     "username1",
     *     "username2",
     *     "username3"...
     * }
     *
     * @param array $data
     *
     * @return mixed
     */
    public function setWhitelistByUsername(array $data)
    {
        return $this->setWhitelist(array('usernames' => $data));
    }

    /**
     * 创建卡券货架.
     *
     * <pre>
     * $data:
     * {
     *     "banner": "http://mmbiz.qpic.cn/mmbiz/iaL1LJM1mF9aRKPZJkmG8xXhiaHqkKSVMMWeN3hLut7X7h  icFN",
     *     "page_title": "惠城优惠大派送",
     *     "can_share": true,
     *     "scene": 'SCENE_H5',
     *     "card_list": [
     *         {
     *             'card_id':"pXch-jnOlGtbuWwIO2NDftZeynRE",
     *             'thumb_url':"www.qq.com/a.jpg"
     *         },
     *         {
     *             'card_id':"pXch-jnAN-ZBoRbiwgqBZ1RV60fI",
     *             'thumb_url':"www.qq.com/b.jpg"
     *         }
     *     ]
     * }
     * </pre>
     *
     * @param array $data
     *
     * @return array
     */
    public function createLandingPage(array $data)
    {
        $defaultParams = array(
            'banner' => '',
            'page_title' => '',
            'can_share' => true,
            'scene' => 'SCENE_H5',
            'card_list' => array(
                array(
                    'card_id' => '',
                    'thumb_url' => '',
                ),
            ),
        );

        $params = array_merge($defaultParams, $data);

        return $this->http->jsonPost(self::API_LANDINGPAGE_CREATE, $params);
    }
}
