<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\Card;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Support\Arr;
use EasyWeChat\Kernel\Traits\InteractsWithCache;

/**
 * Class Client.
 *
 * @author overtrue <i@overtrue.me>
 */
class Client extends BaseClient
{
    use InteractsWithCache;

    /**
     * @var string
     */
    protected $url;

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
    protected $ticketCachePrefix = 'easywechat.card_api_ticket.';

    /**
     * 获取卡券颜色.
     *
     * @return mixed
     */
    public function getColors()
    {
        return $this->httpGet('card/getcolors');
    }

    /**
     * 创建卡券.
     *
     * @param string $cardType
     * @param array  $baseInfo
     * @param array  $especial
     * @param array  $advancedInfo
     *
     * @return mixed
     */
    public function create($cardType = 'member_card', array $baseInfo = [], array $especial = [], array $advancedInfo = [])
    {
        $params = [
            'card' => [
                'card_type' => strtoupper($cardType),
                strtolower($cardType) => array_merge(['base_info' => $baseInfo], $especial, ['advanced_info' => $advancedInfo]),
            ],
        ];

        return $this->httpPostJson('card/create', $params);
    }

    /**
     * 创建二维码.
     *
     * @param array $cards
     *
     * @return mixed
     */
    public function createQrCode(array $cards = [])
    {
        return $this->httpPostJson('card/qrcode/create', $cards);
    }

    /**
     * ticket 换取二维码图片.
     *
     * @param string $ticket
     *
     * @return array
     */
    public function showQrCode($ticket = null)
    {
        $baseUri = 'https://mp.weixin.qq.com/cgi-bin/showqrcode';
        $params = [
            'ticket' => $ticket,
        ];

        $response = $this->requestRaw($baseUri, 'GET', $params);

        return [
            'status' => $response->getStatusCode(),
            'reason' => $response->getReasonPhrase(),
            'headers' => $response->getHeaders(),
            'body' => strval($response->getBody()),
            'url' => $baseUri.'?'.http_build_query($params),
        ];
    }

    /**
     * 通过ticket换取二维码 链接.
     *
     * @param string $ticket
     *
     * @return string
     */
    public function getQrCodeUrl($ticket)
    {
        return sprintf('https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=%s', $ticket);
    }

    /**
     * 获取 卡券 Api_ticket.
     *
     * @param bool $refresh 是否强制刷新
     *
     * @return string $apiTicket
     */
    public function getTicket($refresh = false)
    {
        $key = $this->getTicketCacheKey();

        $ticket = $this->getCache()->get($key);

        if (!$ticket || $refresh) {
            $result = $this->httpGet('cgi-bin/ticket/getticket', ['type' => 'wx_card']);

            $this->getCache()->set($key, $ticket = $result['ticket'], $result['expires_in'] - 500);

            return $ticket;
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
     * @return array
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
            $this->getTicket(),
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
     * @return mixed
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

        return $this->httpPostJson('card/landingpage/create', $params);
    }

    /**
     * 导入code接口.
     *
     * @param string $cardId
     * @param array  $code
     *
     * @return mixed
     */
    public function deposit($cardId, $code)
    {
        $params = [
            'card_id' => $cardId,
            'code' => $code,
        ];

        return $this->httpPostJson('card/code/deposit', $params);
    }

    /**
     * 查询导入code数目.
     *
     * @param string $cardId
     *
     * @return mixed
     */
    public function getDepositedCount($cardId)
    {
        $params = [
            'card_id' => $cardId,
        ];

        return $this->httpPostJson('card/code/getdepositcount', $params);
    }

    /**
     * 核查code接口.
     *
     * @param string $cardId
     * @param array  $code
     *
     * @return mixed
     */
    public function checkCode($cardId, $code)
    {
        $params = [
            'card_id' => $cardId,
            'code' => $code,
        ];

        return $this->httpPostJson('card/code/checkcode', $params);
    }

    /**
     * 查询Code接口.
     *
     * @param string $code
     * @param bool   $checkConsume
     * @param string $cardId
     *
     * @return mixed
     */
    public function getCode($code, $checkConsume, $cardId)
    {
        $params = [
            'code' => $code,
            'check_consume' => $checkConsume,
            'card_id' => $cardId,
        ];

        return $this->httpPostJson('card/code/get', $params);
    }

    /**
     * 核销Code接口.
     *
     * @param string $code
     * @param string $cardId
     *
     * @return mixed
     */
    public function consume($code, $cardId = null)
    {
        if (strlen($code) === 28 && $cardId && strlen($cardId) !== 28) {
            list($code, $cardId) = [$cardId, $code];
        }

        $params = [
            'code' => $code,
        ];

        if ($cardId) {
            $params['card_id'] = $cardId;
        }

        return $this->httpPostJson('card/code/consume', $params);
    }

    /**
     * Code解码接口.
     *
     * @param string $encryptedCode
     *
     * @return mixed
     */
    public function decryptCode($encryptedCode)
    {
        $params = [
            'encrypt_code' => $encryptedCode,
        ];

        return $this->httpPostJson('card/code/decrypt', $params);
    }

    /**
     * 图文消息群发卡券.
     *
     * @param string $cardId
     *
     * @return mixed
     */
    public function getHtml($cardId)
    {
        $params = [
            'card_id' => $cardId,
        ];

        return $this->httpPostJson('card/mpnews/gethtml', $params);
    }

    /**
     * 设置测试白名单.
     *
     * @param array $openids
     *
     * @return mixed
     */
    public function setTestWhitelist($openids)
    {
        $params = [
            'openid' => $openids,
        ];

        return $this->httpPostJson('card/testwhitelist/set', $params);
    }

    /**
     * 设置测试白名单(by username).
     *
     * @param array $usernames
     *
     * @return mixed
     */
    public function setTestWhitelistByUsername($usernames)
    {
        $params = [
            'username' => $usernames,
        ];

        return $this->httpPostJson('card/testwhitelist/set', $params);
    }

    /**
     * 获取用户已领取卡券接口.
     *
     * @param string $openid
     * @param string $cardId
     *
     * @return mixed
     */
    public function getUserCards($openid, $cardId = '')
    {
        $params = [
            'openid' => $openid,
            'card_id' => $cardId,
        ];

        return $this->httpPostJson('card/user/getcardlist', $params);
    }

    /**
     * 查看卡券详情.
     *
     * @param string $cardId
     *
     * @return mixed
     */
    public function get($cardId)
    {
        $params = [
            'card_id' => $cardId,
        ];

        return $this->httpPostJson('card/get', $params);
    }

    /**
     * 批量查询卡列表.
     *
     * @param int    $offset
     * @param int    $count
     * @param string $statusList
     *
     * @return mixed
     */
    public function lists($offset = 0, $count = 10, $statusList = 'CARD_STATUS_VERIFY_OK')
    {
        $params = [
            'offset' => $offset,
            'count' => $count,
            'status_list' => $statusList,
        ];

        return $this->httpPostJson('card/batchget', $params);
    }

    /**
     * 更改卡券信息接口 and 设置跟随推荐接口.
     *
     * @param string $cardId
     * @param string $type
     * @param array  $baseInfo
     * @param array  $especial
     *
     * @return mixed
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

        return $this->httpPostJson('card/update', $card);
    }

    /**
     * 设置微信买单接口.
     * 设置买单的 card_id 必须已经配置了门店，否则会报错.
     *
     * @param string $cardId
     * @param bool   $isOpen
     *
     * @return mixed
     */
    public function setPayCell($cardId, $isOpen = true)
    {
        $params = [
            'card_id' => $cardId,
            'is_open' => $isOpen,
        ];

        return $this->httpPostJson('card/paycell/set', $params);
    }

    /**
     * 增加库存.
     *
     * @param string $cardId
     * @param int    $amount
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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
     * @return mixed
     */
    protected function updateStock($cardId, $amount, $action = 'increase')
    {
        $key = $action === 'increase' ? 'increase_stock_value' : 'reduce_stock_value';
        $params = [
            'card_id' => $cardId,
            $key => abs($amount),
        ];

        return $this->httpPostJson('card/modifystock', $params);
    }

    /**
     * 更改Code接口.
     *
     * @param string $code
     * @param string $newCode
     * @param array  $cardId
     *
     * @return mixed
     */
    public function updateCode($code, $newCode, $cardId = [])
    {
        $params = [
            'code' => $code,
            'new_code' => $newCode,
            'card_id' => $cardId,
        ];

        return $this->httpPostJson('card/code/update', $params);
    }

    /**
     * 删除卡券接口.
     *
     * @param string $cardId
     *
     * @return mixed
     */
    public function delete($cardId)
    {
        $params = [
            'card_id' => $cardId,
        ];

        return $this->httpPostJson('card/delete', $params);
    }

    /**
     * 设置卡券失效.
     *
     * @param string $code
     * @param string $cardId
     *
     * @return mixed
     */
    public function disable($code, $cardId = '')
    {
        $params = [
            'code' => $code,
            'card_id' => $cardId,
        ];

        return $this->httpPostJson('card/code/unavailable', $params);
    }

    /**
     * 会员卡接口激活.
     *
     * @param array  $info
     * @param string $cardType
     *
     * @return mixed
     */
    public function activate($info = [], $cardType = 'member_card')
    {
        if ($cardType === 'general_card') {
            return $this->httpPostJson('card/generalcard/activate', $info);
        }

        return $this->httpPostJson('card/membercard/activate', $info);
    }

    /**
     * 设置开卡字段接口.
     *
     * @param string $cardId
     * @param array  $requiredForm
     * @param array  $optionalForm
     *
     * @return mixed
     */
    public function activateUserForm($cardId, array $requiredForm = [], array $optionalForm = [])
    {
        $params = array_merge(['card_id' => $cardId], $requiredForm, $optionalForm);

        return $this->httpPostJson('card/membercard/activateuserform/set', $params);
    }

    /**
     * 拉取会员信息接口.
     *
     * @param string $cardId
     * @param string $code
     *
     * @return mixed
     */
    public function getMemberCardUser($cardId, $code)
    {
        $params = [
            'card_id' => $cardId,
            'code' => $code,
        ];

        return $this->httpPostJson('card/membercard/userinfo/get', $params);
    }

    /**
     * 更新会员信息.
     *
     * @param array $params
     *
     * @return mixed
     */
    public function updateMemberCardUser(array $params = [])
    {
        return $this->httpPostJson('card/membercard/updateuser', $params);
    }

    /**
     * 更新通用员信息.
     *
     * @param array $params
     *
     * @return mixed
     */
    public function updateGeneralCardUser(array $params = [])
    {
        return $this->httpPostJson('card/generalcard/updateuser', $params);
    }

    /**
     * 添加子商户.
     *
     * @param array $info
     *
     * @return mixed
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

        return $this->httpPostJson('card/submerchant/submit', $params);
    }

    /**
     * 更新子商户.
     *
     * @param int   $merchantId
     * @param array $info
     *
     * @return mixed
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

        return $this->httpPostJson('card/submerchant/update', $params);
    }

    /**
     * 获取子商户信息.
     *
     * @param int $merchantId
     *
     * @return mixed
     */
    public function getSubMerchant($merchantId)
    {
        return $this->httpPostJson('card/submerchant/get', ['merchant_id' => $merchantId]);
    }

    /**
     * 批量获取子商户信息.
     *
     * @param int    $beginId
     * @param int    $limit
     * @param string $status
     *
     * @return mixed
     */
    public function listSubMerchants($beginId = 0, $limit = 50, $status = 'CHECKING')
    {
        $params = [
            'begin_id' => $beginId,
            'limit' => $limit,
            'status' => $status,
        ];

        return $this->httpPostJson('card/submerchant/batchget', $params);
    }

    /**
     * 卡券开放类目查询接口.
     *
     * @return mixed
     */
    public function getCategories()
    {
        return $this->httpGet('card/getapplyprotocol');
    }

    /**
     * Set Api_ticket cache prefix.
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
            return $this->ticketCachePrefix.$this->getAccessToken()->getClientId();
        }

        return $this->ticketCacheKey;
    }

    /**
     * Set current url.
     *
     * @param string $url
     *
     * @return Client
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
}
