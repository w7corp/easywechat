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
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Card;

use EasyWeChat\Cache\Manager as Cache;
use EasyWeChat\Core\Http;
use EasyWeChat\Support\Arr;
use EasyWeChat\Support\Collection;

/**
 * Class Card.
 */
class Card
{
    /**
     * Http client.
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

    /**
     * Constructor.
     *
     * @param Http  $http
     * @param Cache $cache
     */
    public function __construct(Http $http, Cache $cache)
    {
        $this->http = $http->setExpectedException(CardHttpException::class);
        $this->cache = $cache;
    }

    /**
     * Get JSSDK ticket.
     *
     * @return string
     */
    public function getTicket()
    {
        if ($this->ticket) {
            return $this->ticket;
        }

        $key = 'overtrue.wechat.card.api_ticket';

        return $this->ticket = $this->cache->get(
            $key,
            function ($key) {
                $result = $this->http->get(self::API_TICKET);

                $this->cache->set($key, $result['ticket'], $result['expires_in']);

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
    public function attachExtension($cardId, array $extension = [])
    {
        $timestamp = time();

        $ext = [
                'code' => Arr::get($extension, 'code'),
                'openid' => Arr::get($extension, 'openid', Arr::get($extension, 'open_id')),
                'timestamp' => $timestamp,
                'outer_id' => Arr::get($extension, 'outer_id'),
                'balance' => Arr::get($extension, 'balance'),
               ];

        $ext['signature'] = $this->getSignature(
            $this->getTicket(),
            $timestamp,
            $cardId,
            $ext['code'],
            $ext['openid'],
            $ext['balance']
        );

        return [
                'card_id' => $cardId,
                'card_ext' => json_encode($ext, JSON_UNESCAPED_UNICODE),
               ];
    }

    /**
     * Create card.
     *
     * @param array  $base
     * @param array  $properties
     * @param string $type
     *
     * @return string
     */
    public function create(array $base, array $properties = [], $type = self::GENERAL_COUPON)
    {
        $key = strtolower($type);
        $card = array_merge(['base_info' => $base], $properties);
        $params = [
                   'card' => [
                              'card_type' => $type,
                              $key => $card,
                             ],
                  ];

        $result = $this->http->json(self::API_CREATE, $params);

        return $result['card_id'];
    }

    /**
     * Get card detail.
     *
     * @param string $cardId
     *
     * @return Collection
     */
    public function get($cardId)
    {
        $params = ['card_id' => $cardId];

        $result = $this->http->json(self::API_GET, $params);

        return new Collection($result['card']);
    }

    /**
     * Update card.
     *
     * @param string $cardId
     * @param string $type
     * @param array  $base
     * @param array  $data
     *
     * @return bool
     */
    public function update($cardId, $type, array $base = [], array $data = [])
    {
        $key = strtolower($type);
        $card = array_merge(['base_info' => $base], $data);

        $params = [
                   'card_id' => $cardId,
                   $key => $card,
                  ];

        return $this->http->json(self::API_UPDATE, $params);
    }

    /**
     * Batch get card list.
     *
     * @param int $offset
     * @param int $count
     *
     * @return array
     */
    public function lists($offset = 0, $count = 10)
    {
        $params = [
                   'offset' => $offset,
                   'count' => $count,
                  ];

        $result = $this->http->json(self::API_LIST, $params);

        return $result['card_id_list'];
    }

    /**
     * Confirm consume.
     *
     * @param string $code
     * @param string $cardId
     *
     * @return Collection
     */
    public function consume($code, $cardId = null)
    {
        $params = [
                   'code' => $code,
                   'card_id' => $cardId,
                  ];

        return new Collection($this->http->json(self::API_CONSUME, $params));
    }

    /**
     * Disable card.
     *
     * @param string $code
     * @param string $cardId
     *
     * @return bool
     */
    public function disable($code, $cardId = null)
    {
        $params = [
                   'code' => $code,
                   'card_id' => $cardId,
                  ];

        return $this->http->json(self::API_UNAVAILABLE, $params);
    }

    /**
     * Delete card.
     *
     * @param string $cardId
     *
     * @return bool
     */
    public function delete($cardId)
    {
        $params = ['card_id' => $cardId];

        return $this->http->json(self::API_DELETE, $params);
    }

    /**
     * Change stock.
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

        $params = [
                   'card_id' => $cardId,
                   $key => abs($amount),
                  ];

        return $this->http->json(self::API_UPDATE_STOCK, $params);
    }

    /**
     * Increment stock.
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
     * Decrement stock.
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
     * Get code of card.
     *
     * @param string $code
     * @param string $cardId
     *
     * @return Collection
     */
    public function getCode($code, $cardId = null)
    {
        $params = [
                   'code' => $code,
                   'card_id' => $cardId,
                  ];

        return new Collection($this->http->json(self::API_CODE_GET, $params));
    }

    /**
     * Update code of card.
     *
     * @param string $code
     * @param string $newCode
     * @param string $cardId
     *
     * @return bool
     */
    public function updateCode($code, $newCode, $cardId)
    {
        $params = [
                   'code' => $code,
                   'card_id' => $cardId,
                   'new_code' => $newCode,
                  ];

        return $this->http->json(self::API_CODE_UPDATE, $params);
    }

    /**
     * Decode code.
     *
     * @param string $encryptedCode
     *
     * @return string
     */
    public function getRealCode($encryptedCode)
    {
        $params = ['encrypt_code' => $encryptedCode];

        $result = $this->http->json(self::API_CODE_DECRYPT, $params);

        return $result['code'];
    }

    /**
     * Active member card.
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
        $params = array_merge(['card_id' => $cardId], $data);

        return $this->http->json(self::API_MEMBER_CARD_ACTIVE, $params);
    }

    /**
     * Trade member card.
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
     * @return Collection
     */
    public function memberCardTrade($cardId, array $data)
    {
        $params = array_merge(['card_id' => $cardId], $data);

        return new Collection($this->http->json(self::API_MEMBER_CARD_TRADE, $params));
    }

    /**
     * Update ticket.
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
     * @param stirng $cardId
     * @param array  $data
     *
     * @return bool
     */
    public function updateMovieTicket($cardId, array $data)
    {
        $params = array_merge(['card_id' => $cardId], $data);

        return $this->http->json(self::API_MOVIE_TICKET_UPDATE, $params);
    }

    /**
     * Update meeting ticket.
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
        $params = array_merge(['card_id' => $cardId], $data);

        return $this->http->json(self::API_MEETING_TICKET_UPDATE, $params);
    }

    /**
     * Checkin.
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
        $params = array_merge(['card_id' => $cardId], $data);

        return $this->http->json(self::API_BOARDING_PASS_CHECKIN, $params);
    }

    /**
     * Return signature.
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
     * Return random string.
     *
     * @return string
     */
    public function getNonce()
    {
        return uniqid('pre_');
    }
}
