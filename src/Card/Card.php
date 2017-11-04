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
 * @copyright 2016 overtrue <i@overtrue.me>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Card;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;
use EasyWeChat\Core\AbstractAPI;
use EasyWeChat\Support\Arr;
use Psr\Http\Message\ResponseInterface;

class Card extends AbstractAPI
{
    /**
     * Cache.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Ticket cache key.
     *
     * @var string
     */
    protected $ticketCacheKey;

    /**
     * Ticket cache prefix.
     *
     * @var string
     */
    protected $ticketCachePrefix = 'overtrue.wechat.card_api_ticket.';

    const API_GET_COLORS = 'https://api.weixin.qq.com/card/getcolors';
    const API_CREATE_CARD = 'https://api.weixin.qq.com/card/create';
    const API_CREATE_QRCODE = 'https://api.weixin.qq.com/card/qrcode/create';
    const API_SHOW_QRCODE = 'https://mp.weixin.qq.com/cgi-bin/showqrcode';
    const API_GET_CARD_TICKET = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket';
    const API_CREATE_LANDING_PAGE = 'https://api.weixin.qq.com/card/landingpage/create';
    const API_DEPOSIT_CODE = 'https://api.weixin.qq.com/card/code/deposit';
    const API_GET_DEPOSIT_COUNT = 'https://api.weixin.qq.com/card/code/getdepositcount';
    const API_CHECK_CODE = 'https://api.weixin.qq.com/card/code/checkcode';
    const API_GET_HTML = 'https://api.weixin.qq.com/card/mpnews/gethtml';
    const API_SET_TEST_WHITE_LIST = 'https://api.weixin.qq.com/card/testwhitelist/set';
    const API_GET_CODE = 'https://api.weixin.qq.com/card/code/get';
    const API_CONSUME_CARD = 'https://api.weixin.qq.com/card/code/consume';
    const API_DECRYPT_CODE = 'https://api.weixin.qq.com/card/code/decrypt';
    const API_GET_CARD_LIST = 'https://api.weixin.qq.com/card/user/getcardlist';
    const API_GET_CARD = 'https://api.weixin.qq.com/card/get';
    const API_LIST_CARD = 'https://api.weixin.qq.com/card/batchget';
    const API_UPDATE_CARD = 'https://api.weixin.qq.com/card/update';
    const API_SET_PAY_CELL = 'https://api.weixin.qq.com/card/paycell/set';
    const API_MODIFY_STOCK = 'https://api.weixin.qq.com/card/modifystock';
    const API_UPDATE_CODE = 'https://api.weixin.qq.com/card/code/update';
    const API_DELETE_CARD = 'https://api.weixin.qq.com/card/delete';
    const API_DISABLE_CARD = 'https://api.weixin.qq.com/card/code/unavailable';
    const API_ACTIVATE_MEMBER_CARD = 'https://api.weixin.qq.com/card/membercard/activate';
    const API_ACTIVATE_MEMBER_USER_FORM = 'https://api.weixin.qq.com/card/membercard/activateuserform/set';
    const API_GET_MEMBER_USER_INFO = 'https://api.weixin.qq.com/card/membercard/userinfo/get';
    const API_UPDATE_MEMBER_CARD_USER = 'https://api.weixin.qq.com/card/membercard/updateuser';
    const API_CREATE_SUB_MERCHANT = 'https://api.weixin.qq.com/card/submerchant/submit';
    const API_UPDATE_SUB_MERCHANT = 'https://api.weixin.qq.com/card/submerchant/update';
    const API_GET_SUB_MERCHANT = 'https://api.weixin.qq.com/card/submerchant/get';
    const API_LIST_SUB_MERCHANT = 'https://api.weixin.qq.com/card/submerchant/batchget';
    const API_GET_CATEGORIES = 'https://api.weixin.qq.com/card/getapplyprotocol';
    const API_ACTIVATE_GENERAL_CARD = 'https://api.weixin.qq.com/card/generalcard/activate';
    const API_UPDATE_GENERAL_CARD_USER = 'https://api.weixin.qq.com/card/generalcard/updateuser';

    /**
     * 获取卡券颜色.
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getColors()
    {
        return $this->parseJSON('get', [self::API_GET_COLORS]);
    }

    /**
     * 创建卡券.
     *
     * @param string $cardType
     * @param array  $baseInfo
     * @param array  $especial
     * @param array  $advancedInfo
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function create($cardType = 'member_card', array $baseInfo = [], array $especial = [], array $advancedInfo = [])
    {
        $params = [
            'card' => [
                'card_type' => strtoupper($cardType),
                strtolower($cardType) => array_merge(['base_info' => $baseInfo], $especial, ['advanced_info' => $advancedInfo]),
            ],
        ];

        return $this->parseJSON('json', [self::API_CREATE_CARD, $params]);
    }

    /**
     * 创建二维码.
     *
     * @param array $cards
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function QRCode(array $cards = [])
    {
        return $this->parseJSON('json', [self::API_CREATE_QRCODE, $cards]);
    }

    /**
     * ticket 换取二维码图片.
     *
     * @param string $ticket
     *
     * @return array
     */
    public function showQRCode($ticket = null)
    {
        $params = [
            'ticket' => $ticket,
        ];

        $http = $this->getHttp();

        /** @var ResponseInterface $response */
        $response = $http->get(self::API_SHOW_QRCODE, $params);

        return [
            'status' => $response->getStatusCode(),
            'reason' => $response->getReasonPhrase(),
            'headers' => $response->getHeaders(),
            'body' => strval($response->getBody()),
            'url' => self::API_SHOW_QRCODE.'?'.http_build_query($params),
        ];
    }

    /**
     * 通过ticket换取二维码 链接.
     *
     * @param string $ticket
     *
     * @return string
     */
    public function getQRCodeUrl($ticket)
    {
        return self::API_SHOW_QRCODE.'?ticket='.$ticket;
    }

    /**
     * 获取 卡券 Api_ticket.
     *
     * @param bool $refresh 是否强制刷新
     *
     * @return string $apiTicket
     */
    public function getAPITicket($refresh = false)
    {
        $key = $this->getTicketCacheKey();

        $ticket = $this->getCache()->fetch($key);

        if (!$ticket || $refresh) {
            $result = $this->parseJSON('get', [self::API_GET_CARD_TICKET, ['type' => 'wx_card']]);

            $this->getCache()->save($key, $result['ticket'], $result['expires_in'] - 500);

            return $result['ticket'];
        }

        return $ticket;
    }

    /**
     * 微信卡券：JSAPI 卡券发放.
     *
     * @param array $cards
     *
     * @return string
     */
    public function jsConfigForAssign(array $cards)
    {
        return json_encode(array_map(function ($card) {
            return $this->attachExtension($card['card_id'], $card);
        }, $cards));
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
            'fixed_begintimestamp' => Arr::get($extension, 'fixed_begintimestamp'),
            'outer_str' => Arr::get($extension, 'outer_str'),
        ];
        $ext['signature'] = $this->getSignature(
            $this->getAPITicket(),
            $timestamp,
            $cardId,
            $ext['code'],
            $ext['openid'],
            $ext['balance']
        );

        return [
            'cardId' => $cardId,
            'cardExt' => json_encode($ext),
        ];
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
     * 创建货架接口.
     *
     * @param string $banner
     * @param string $pageTitle
     * @param bool   $canShare
     * @param string $scene     [SCENE_NEAR_BY 附近,SCENE_MENU 自定义菜单,SCENE_QRCODE 二维码,SCENE_ARTICLE 公众号文章,
     *                          SCENE_H5 h5页面,SCENE_IVR 自动回复,SCENE_CARD_CUSTOM_CELL 卡券自定义cell]
     * @param array  $cardList
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function createLandingPage($banner, $pageTitle, $canShare, $scene, $cardList)
    {
        $params = [
            'banner' => $banner,
            'page_title' => $pageTitle,
            'can_share' => $canShare,
            'scene' => $scene,
            'card_list' => $cardList,
        ];

        return $this->parseJSON('json', [self::API_CREATE_LANDING_PAGE, $params]);
    }

    /**
     * 导入code接口.
     *
     * @param string $cardId
     * @param array  $code
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function deposit($cardId, $code)
    {
        $params = [
            'card_id' => $cardId,
            'code' => $code,
        ];

        return $this->parseJSON('json', [self::API_DEPOSIT_CODE, $params]);
    }

    /**
     * 查询导入code数目.
     *
     * @param string $cardId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getDepositedCount($cardId)
    {
        $params = [
            'card_id' => $cardId,
        ];

        return $this->parseJSON('json', [self::API_GET_DEPOSIT_COUNT, $params]);
    }

    /**
     * 核查code接口.
     *
     * @param string $cardId
     * @param array  $code
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function checkCode($cardId, $code)
    {
        $params = [
            'card_id' => $cardId,
            'code' => $code,
        ];

        return $this->parseJSON('json', [self::API_CHECK_CODE, $params]);
    }

    /**
     * 查询Code接口.
     *
     * @param string $code
     * @param bool   $checkConsume
     * @param string $cardId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getCode($code, $checkConsume, $cardId)
    {
        $params = [
            'code' => $code,
            'check_consume' => $checkConsume,
            'card_id' => $cardId,
        ];

        return $this->parseJSON('json', [self::API_GET_CODE, $params]);
    }

    /**
     * 核销Code接口.
     *
     * @param string $code
     * @param string $cardId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function consume($code, $cardId = null)
    {
        if (28 === strlen($code) && $cardId && 28 !== strlen($cardId)) {
            list($code, $cardId) = [$cardId, $code];
        }

        $params = [
            'code' => $code,
        ];

        if ($cardId) {
            $params['card_id'] = $cardId;
        }

        return $this->parseJSON('json', [self::API_CONSUME_CARD, $params]);
    }

    /**
     * Code解码接口.
     *
     * @param string $encryptedCode
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function decryptCode($encryptedCode)
    {
        $params = [
            'encrypt_code' => $encryptedCode,
        ];

        return $this->parseJSON('json', [self::API_DECRYPT_CODE, $params]);
    }

    /**
     * 图文消息群发卡券.
     *
     * @param string $cardId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getHtml($cardId)
    {
        $params = [
            'card_id' => $cardId,
        ];

        return $this->parseJSON('json', [self::API_GET_HTML, $params]);
    }

    /**
     * 设置测试白名单.
     *
     * @param array $openids
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function setTestWhitelist($openids)
    {
        $params = [
            'openid' => $openids,
        ];

        return $this->parseJSON('json', [self::API_SET_TEST_WHITE_LIST, $params]);
    }

    /**
     * 设置测试白名单(by username).
     *
     * @param array $usernames
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function setTestWhitelistByUsername($usernames)
    {
        $params = [
            'username' => $usernames,
        ];

        return $this->parseJSON('json', [self::API_SET_TEST_WHITE_LIST, $params]);
    }

    /**
     * 获取用户已领取卡券接口.
     *
     * @param string $openid
     * @param string $cardId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getUserCards($openid, $cardId = '')
    {
        $params = [
            'openid' => $openid,
            'card_id' => $cardId,
        ];

        return $this->parseJSON('json', [self::API_GET_CARD_LIST, $params]);
    }

    /**
     * 查看卡券详情.
     *
     * @param string $cardId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getCard($cardId)
    {
        $params = [
            'card_id' => $cardId,
        ];

        return $this->parseJSON('json', [self::API_GET_CARD, $params]);
    }

    /**
     * 批量查询卡列表.
     *
     * @param int    $offset
     * @param int    $count
     * @param string $statusList
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function lists($offset = 0, $count = 10, $statusList = 'CARD_STATUS_VERIFY_OK')
    {
        $params = [
            'offset' => $offset,
            'count' => $count,
            'status_list' => $statusList,
        ];

        return $this->parseJSON('json', [self::API_LIST_CARD, $params]);
    }

    /**
     * 更改卡券信息接口 and 设置跟随推荐接口.
     *
     * @param string $cardId
     * @param string $type
     * @param array  $baseInfo
     * @param array  $especial
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function update($cardId, $type, $baseInfo = [], $especial = [])
    {
        $card = [];
        $card['card_id'] = $cardId;
        $card[$type] = [];

        $cardInfo = [];
        if ($baseInfo) {
            $cardInfo['base_info'] = $baseInfo;
        }

        $card[$type] = array_merge($cardInfo, $especial);

        return $this->parseJSON('json', [self::API_UPDATE_CARD, $card]);
    }

    /**
     * 设置微信买单接口.
     * 设置买单的 card_id 必须已经配置了门店，否则会报错.
     *
     * @param string $cardId
     * @param bool   $isOpen
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function setPayCell($cardId, $isOpen = true)
    {
        $params = [
            'card_id' => $cardId,
            'is_open' => $isOpen,
        ];

        return $this->parseJSON('json', [self::API_SET_PAY_CELL, $params]);
    }

    /**
     * 增加库存.
     *
     * @param string $cardId
     * @param int    $amount
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function increaseStock($cardId, $amount)
    {
        return $this->updateStock($cardId, $amount, 'increase');
    }

    /**
     * 减少库存.
     *
     * @param string $cardId
     * @param int    $amount
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function reduceStock($cardId, $amount)
    {
        return $this->updateStock($cardId, $amount, 'reduce');
    }

    /**
     * 修改库存接口.
     *
     * @param string $cardId
     * @param int    $amount
     * @param string $action
     *
     * @return \EasyWeChat\Support\Collection
     */
    protected function updateStock($cardId, $amount, $action = 'increase')
    {
        $key = 'increase' === $action ? 'increase_stock_value' : 'reduce_stock_value';
        $params = [
            'card_id' => $cardId,
            $key => abs($amount),
        ];

        return $this->parseJSON('json', [self::API_MODIFY_STOCK, $params]);
    }

    /**
     * 更改Code接口.
     *
     * @param string $code
     * @param string $newCode
     * @param array  $cardId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function updateCode($code, $newCode, $cardId = [])
    {
        $params = [
            'code' => $code,
            'new_code' => $newCode,
            'card_id' => $cardId,
        ];

        return $this->parseJSON('json', [self::API_UPDATE_CODE, $params]);
    }

    /**
     * 删除卡券接口.
     *
     * @param string $cardId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function delete($cardId)
    {
        $params = [
            'card_id' => $cardId,
        ];

        return $this->parseJSON('json', [self::API_DELETE_CARD, $params]);
    }

    /**
     * 设置卡券失效.
     *
     * @param string $code
     * @param string $cardId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function disable($code, $cardId = '')
    {
        $params = [
            'code' => $code,
            'card_id' => $cardId,
        ];

        return $this->parseJSON('json', [self::API_DISABLE_CARD, $params]);
    }

    /**
     * 会员卡接口激活.
     *
     * @param array $info
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function activate($info = [], $cardType = 'member_card')
    {
        if ('general_card' === $cardType) {
            return $this->parseJSON('json', [self::API_ACTIVATE_GENERAL_CARD, $info]);
        }

        return $this->parseJSON('json', [self::API_ACTIVATE_MEMBER_CARD, $info]);
    }

    /**
     * 设置开卡字段接口.
     *
     * @param string $cardId
     * @param array  $requiredForm
     * @param array  $optionalForm
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function activateUserForm($cardId, array $requiredForm = [], array $optionalForm = [])
    {
        $params = array_merge(['card_id' => $cardId], $requiredForm, $optionalForm);

        return $this->parseJSON('json', [self::API_ACTIVATE_MEMBER_USER_FORM, $params]);
    }

    /**
     * 拉取会员信息接口.
     *
     * @param string $cardId
     * @param string $code
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getMemberCardUser($cardId, $code)
    {
        $params = [
            'card_id' => $cardId,
            'code' => $code,
        ];

        return $this->parseJSON('json', [self::API_GET_MEMBER_USER_INFO, $params]);
    }

    /**
     * 更新会员信息.
     *
     * @param array $params
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function updateMemberCardUser(array $params = [])
    {
        return $this->parseJSON('json', [self::API_UPDATE_MEMBER_CARD_USER, $params]);
    }

    /**
     * 更新通用员信息.
     *
     * @param array $params
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function updateGeneralCardUser(array $params = [])
    {
        return $this->parseJSON('json', [self::API_UPDATE_GENERAL_CARD_USER, $params]);
    }

    /**
     * 添加子商户.
     *
     * @param array $info
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function createSubMerchant(array $info = [])
    {
        $params = [
            'info' => Arr::only($info, [
                'brand_name',
                'logo_url',
                'protocol',
                'end_time',
                'primary_category_id',
                'secondary_category_id',
                'agreement_media_id',
                'operator_media_id',
                'app_id',
            ]),
        ];

        return $this->parseJSON('json', [self::API_CREATE_SUB_MERCHANT, $params]);
    }

    /**
     * 更新子商户.
     *
     * @param int   $merchantId
     * @param array $info
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function updateSubMerchant($merchantId, array $info = [])
    {
        $params = [
            'info' => array_merge(['merchant_id' => $merchantId],
                Arr::only($info, [
                    'brand_name',
                    'logo_url',
                    'protocol',
                    'end_time',
                    'primary_category_id',
                    'secondary_category_id',
                    'agreement_media_id',
                    'operator_media_id',
                    'app_id',
                ])),
        ];

        return $this->parseJSON('json', [self::API_UPDATE_SUB_MERCHANT, $params]);
    }

    /**
     * 获取子商户信息.
     *
     * @param int $merchantId
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getSubMerchant($merchantId)
    {
        return $this->parseJSON('json', [self::API_GET_SUB_MERCHANT, ['merchant_id' => $merchantId]]);
    }

    /**
     * 批量获取子商户信息.
     *
     * @param int    $beginId
     * @param int    $limit
     * @param string $status
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function listSubMerchants($beginId = 0, $limit = 50, $status = 'CHECKING')
    {
        $params = [
            'begin_id' => $beginId,
            'limit' => $limit,
            'status' => $status,
        ];

        return $this->parseJSON('json', [self::API_LIST_SUB_MERCHANT, $params]);
    }

    /**
     * 卡券开放类目查询接口.
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getCategories()
    {
        return $this->parseJSON('get', [self::API_GET_CATEGORIES]);
    }

    /**
     * Set cache manager.
     *
     * @param \Doctrine\Common\Cache\Cache $cache
     *
     * @return $this
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Return cache manager.
     *
     * @return \Doctrine\Common\Cache\Cache
     */
    public function getCache()
    {
        return $this->cache ?: $this->cache = new FilesystemCache(sys_get_temp_dir());
    }

    /**
     * Set Api_ticket cache prifix.
     *
     * @param string $prefix
     *
     * @return $this
     */
    public function setTicketCachePrefix($prefix)
    {
        $this->ticketCachePrefix = $prefix;

        return $this;
    }

    /**
     * Set Api_ticket cache key.
     *
     * @param string $cacheKey
     *
     * @return $this
     */
    public function setTicketCacheKey($cacheKey)
    {
        $this->ticketCacheKey = $cacheKey;

        return $this;
    }

    /**
     * Get ApiTicket token cache key.
     *
     * @return string
     */
    public function getTicketCacheKey()
    {
        if (is_null($this->ticketCacheKey)) {
            return $this->ticketCachePrefix.$this->getAccessToken()->getAppId();
        }

        return $this->ticketCacheKey;
    }

    /**
     * Set current url.
     *
     * @param string $url
     *
     * @return Card
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
}
