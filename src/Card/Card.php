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
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */
namespace EasyWeChat\Card;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;
use EasyWeChat\Core\AbstractAPI;
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
     * Ticket cache prefix.
     */
    const TICKET_CACHE_PREFIX = 'overtrue.wechat.card_api_ticket.';

    const API_GET_COLORS = 'https://api.weixin.qq.com/card/getcolors';
    const API_CREATE = 'https://api.weixin.qq.com/card/create';
    const API_QRCODE_CREATE = 'https://api.weixin.qq.com/card/qrcode/create';
    const API_QRCODE_SHOW = 'https://mp.weixin.qq.com/cgi-bin/showqrcode';
    const API_GET_CARD_TICKET = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket';
    const API_LANDING_PAGE = 'https://api.weixin.qq.com/card/landingpage/create';
    const API_DEPOSIT = 'https://api.weixin.qq.com/card/code/deposit';
    const API_GET_DEPOSIT_COUNT = 'https://api.weixin.qq.com/card/code/getdepositcount';
    const API_CHECK_CODE = 'https://api.weixin.qq.com/card/code/checkcode';
    const API_GET_HTML = 'https://api.weixin.qq.com/card/mpnews/gethtml';
    const API_TEST_WHITE_LIST = 'https://api.weixin.qq.com/card/testwhitelist/set';
    const API_CODE_GET = 'https://api.weixin.qq.com/card/code/get';
    const API_CONSUME = 'https://api.weixin.qq.com/card/code/consume';
    const API_DECRYPT = 'https://api.weixin.qq.com/card/code/decrypt';
    const API_GET_CARD_LIST = 'https://api.weixin.qq.com/card/user/getcardlist';
    const API_CARD_GET = 'https://api.weixin.qq.com/card/get';
    const API_BATCH_GET = 'https://api.weixin.qq.com/card/batchget';
    const API_UPDATE = 'https://api.weixin.qq.com/card/update';
    const API_PAY_CELL_SET = 'https://api.weixin.qq.com/card/paycell/set';
    const API_MODIFY_STOCK = 'https://api.weixin.qq.com/card/modifystock';
    const API_CODE_UPDATE = 'https://api.weixin.qq.com/card/code/update';
    const API_CARD_DELETE = 'https://api.weixin.qq.com/card/delete';
    const API_UNAVAILABLE = 'https://api.weixin.qq.com/card/code/unavailable';
    const API_CARD_BIZ_UIN_INFO = 'https://api.weixin.qq.com/datacube/getcardbizuininfo';
    const API_CARD_CARD_INFO = 'https://api.weixin.qq.com/datacube/getcardcardinfo';
    const API_CARD_MEMBER_CARD_INFO = 'https://api.weixin.qq.com/datacube/getcardmembercardinfo';
    const API_CARD_ACTIVATE = 'https://api.weixin.qq.com/card/membercard/activate';
    const API_ACTIVATE_USER_FORM = 'https://api.weixin.qq.com/card/membercard/activateuserform/set';
    const API_MEMBER_USER_INFO = 'https://api.weixin.qq.com/card/membercard/userinfo/get';
    const API_UPDATE_USER = 'https://api.weixin.qq.com/card/membercard/updateuser';
    const API_SUB_MERCHANT = 'https://api.weixin.qq.com/card/submerchant/submit';
    const API_GET_APPLY_PROTOCOL = 'https://api.weixin.qq.com/card/getapplyprotocol';

    /**
     * 获取卡券颜色.
     *
     * @return array
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
     * @return bool|array
     */
    public function create($cardType = 'member_card', $baseInfo = [], $especial = [], $advancedInfo = [])
    {
        $card = [];
        $card['card'] = [];
        $card['card']['card_type'] = strtoupper($cardType);

        $type = strtolower($cardType);

        $card_info = [];
        $card_info['base_info'] = $baseInfo;

        $card['card'][$type] = [];
        $card['card'][$type] = array_merge($card_info, $especial, $advancedInfo);

        if (is_string($cardType) && is_array($baseInfo) && is_array($especial)) {
            return $this->parseJSON('json', [self::API_CREATE, $card]);
        }

        return false;
    }

    /**
     * 创建二维码.
     *
     * @param array $cardList
     *
     * @return array|bool
     */
    public function qrCode($cardList = [])
    {
        return $this->parseJSON('json', [self::API_QRCODE_CREATE, $cardList]);
    }

    /**
     * ticket 换取二维码图片.
     *
     * @param null $ticket
     *
     * @return array
     */
    public function showQrCode($ticket = null)
    {
        $params = [
            'ticket' => $ticket,
        ];

        $http = $this->getHttp();

        /** @var ResponseInterface $response */
        $response = $http->get(self::API_QRCODE_SHOW, $params);

        return [
            'status' => $response->getStatusCode(),
            'reason' => $response->getReasonPhrase(),
            'headers' => $response->getHeaders(),
            'body' => strval($response->getBody()),
            'url' => self::API_QRCODE_SHOW . '?' . http_build_query($params),
        ];
    }

    /**
     * 通过ticket换取二维码 链接.
     *
     * @param $ticket
     *
     * @return string
     */
    public function showQrCodeUrl($ticket)
    {
        $params = '?ticket=' . $ticket;

        return self::API_QRCODE_SHOW . $params;
    }

    /**
     * 获取 卡券 Api_ticket.
     *
     * @param  boolean $isRefresh 是否强制刷新
     *
     * @return string  $apiTicket
     */
    public function cardApiTicket($isRefresh = false)
    {
        $key = self::TICKET_CACHE_PREFIX . $this->getAccessToken()->getAppId();

        $ticket = $this->getCache()->fetch($key);

        if (!$ticket || $isRefresh) {
            $result = $this->parseJSON('get', [self::API_GET_CARD_TICKET, ['type' => 'wx_card']]);

            $res = $this->getCache()->save($key, $result['ticket'], $result['expires_in'] - 500);

            return $result['ticket'];
        }

        return $ticket;
    }

    /**
     * 微信卡券：JSAPI 卡券Package - 基础参数没有附带任何值 - 再生产环境中需要根据实际情况进行修改.
     *
     * @param array $cardList
     * @param null  $timestamp
     * @param null  $apiTicket
     *
     * @return string
     */
    public function wxCardPackage(array $cardList, $timestamp = null, $apiTicket = null)
    {
        if (empty($timestamp) || $timestamp == '') {
            $timestamp = time();
        }

        if (empty($apiTicket) || $apiTicket == '') {
            $apiTicket = $this->cardApiTicket();
        }

        $resultArray = [];
        foreach ($cardList as $key => $value) {
            if (empty($value['code']) || !isset($value['code'])) {
                $value['code'] = '';
            }

            if (empty($value['openid']) || !isset($value['openid'])) {
                $value['openid'] = '';
            }

            $arrays = [$apiTicket, $timestamp, $value['card_id'], $value['code'], $value['openid']];
            sort($arrays, SORT_STRING);
            $string = sha1(implode($arrays));

            $resultArray['cardList'][$key]['cardId'] = $value['card_id'];
            $resultArray['cardList'][$key]['cardExt']['code'] = $value['code'];
            $resultArray['cardList'][$key]['cardExt']['openid'] = $value['openid'];

            $resultArray['cardList'][$key]['cardExt']['timestamp'] = $timestamp;
            $resultArray['cardList'][$key]['cardExt']['signature'] = $string;

            if (!empty($value['outer_id'])) {
                $resultArray['cardList'][$key]['cardExt']['outer_id'] = $value['outer_id'];
            }

            $resultArray['cardList'][$key]['cardExt'] = json_encode($resultArray['cardList'][$key]['cardExt']);
        }

        $resultJson = json_encode($resultArray);

        return $resultJson;
    }

    /**
     * 创建货架接口.
     *
     * @param $banner
     * @param $pageTitle
     * @param $canShare
     * @param $scene
     * @param $cardList
     *
     * @return array
     */
    public function landingPage($banner, $pageTitle, $canShare, $scene, $cardList)
    {
        $params = [
            'banner' => $banner,
            'page_title' => $pageTitle,
            'can_share' => $canShare,
            'scene' => $scene,
            'card_list' => $cardList,
        ];

        return $this->parseJSON('json', [self::API_LANDING_PAGE, $params]);
    }

    /**
     * 导入code接口.
     *
     * @param $cardId
     * @param $code
     *
     * @return array
     */
    public function deposit($cardId, $code)
    {
        $params = [
            'card_id' => $cardId,
            'code' => $code,
        ];

        return $this->parseJSON('json', [self::API_DEPOSIT, $params]);
    }

    /**
     * 查询导入code数目.
     *
     * @param $cardId
     *
     * @return array
     */
    public function getDepositCount($cardId)
    {
        $params = [
            'card_id' => $cardId,
        ];

        return $this->parseJSON('json', [self::API_GET_DEPOSIT_COUNT, $params]);
    }

    /**
     * 核查code接口.
     *
     * @param $cardId
     * @param $code
     *
     * @return array
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
     * 图文消息群发卡券.
     *
     * @param $cardId
     *
     * @return array
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
     * @param $openid
     * @param $username
     *
     * @return array
     */
    public function testWhiteList($openid, $username)
    {
        $params = [
            'openid' => $openid,
            'username' => $username,
        ];

        return $this->parseJSON('json', [self::API_TEST_WHITE_LIST, $params]);
    }

    /**
     * 查询Code接口.
     *
     * @param $code
     * @param $checkConsume
     * @param $cardId
     *
     * @return array
     */
    public function codeGet($code, $checkConsume, $cardId)
    {
        $params = [
            'code' => $code,
            'check_consume' => $checkConsume,
            'card_id' => $cardId,
        ];

        return $this->parseJSON('json', [self::API_CODE_GET, $params]);
    }

    /**
     * 核销Code接口.
     *
     * @param $cardId
     * @param $code
     *
     * @return array
     */
    public function consume($cardId, $code)
    {
        $params = [
            'card_id' => $cardId,
            'code' => $code,
        ];

        return $this->parseJSON('json', [self::API_CONSUME, $params]);
    }

    /**
     * Code解码接口.
     *
     * @param $encryptCode
     *
     * @return array
     */
    public function decrypt($encryptCode)
    {
        $params = [
            'encrypt_code' => $encryptCode,
        ];

        return $this->parseJSON('json', [self::API_DECRYPT, $params]);
    }

    /**
     * 获取用户已领取卡券接口.
     *
     * @param        $openid
     * @param string $cardId
     *
     * @return array
     */
    public function getCardList($openid, $cardId = '')
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
     * @param $cardId
     *
     * @return array
     */
    public function cardGet($cardId)
    {
        $params = [
            'card_id' => $cardId,
        ];

        return $this->parseJSON('json', [self::API_CARD_GET, $params]);
    }

    /**
     * 批量查询卡列表.
     *
     * @param int    $offset
     * @param int    $count
     * @param string $statusList
     *
     * @return array
     */
    public function batchGet($offset = 0, $count = 10, $statusList = 'CARD_STATUS_VERIFY_OK')
    {
        $params = [
            'offset' => $offset,
            'count' => $count,
            'status_list' => $statusList,
        ];

        return $this->parseJSON('json', [self::API_BATCH_GET, $params]);
    }

    /**
     * 更改卡券信息接口 and 设置跟随推荐接口.
     *
     * @param       $cardId
     * @param       $type
     * @param array $baseInfo
     * @param array $especial
     *
     * @return array
     */
    public function update($cardId, $type, $baseInfo = [], $especial = [])
    {
        $card = [];
        $card['card_id'] = $cardId;
        $card[$type] = [];

        $cardInfo = [];
        $cardInfo['base_info'] = $baseInfo;

        $card[$type] = array_merge($cardInfo, $especial);

        return $this->parseJSON('json', [self::API_UPDATE, $card]);
    }

    /**
     * 设置微信买单接口.
     * 设置买单的card_id必须已经配置了门店，否则会报错.
     *
     * @param      $cardId
     * @param bool $isOpen
     *
     * @return array
     */
    public function payCellSet($cardId, $isOpen = true)
    {
        $params = [
            'card_id' => $cardId,
            'is_open' => $isOpen,
        ];

        return $this->parseJSON('json', [self::API_PAY_CELL_SET, $params]);
    }

    /**
     * 修改库存接口.
     *
     * @param        $cardId
     * @param string $stock
     * @param int    $value
     *
     * @return array
     */
    public function modifyStock($cardId, $stock = 'increase', $value = 0)
    {
        $params = [];
        $params['card_id'] = $cardId;
        if ($stock == 'increase') {
            $params['increase_stock_value'] = intval($value);
        } elseif ($stock == 'reduce') {
            $params['reduce_stock_value'] = intval($value);
        } else {
            return false;
        }

        return $this->parseJSON('json', [self::API_MODIFY_STOCK, $params]);
    }

    /**
     * 更改Code接口.
     *
     * @param       $code
     * @param       $newCode
     * @param array $cardId
     *
     * @return array
     */
    public function codeUpdate($code, $newCode, $cardId = [])
    {
        $params = [
            'code' => $code,
            'new_code' => $newCode,
            'card_id' => $cardId,
        ];

        return $this->parseJSON('json', [self::API_CODE_UPDATE, $params]);
    }

    /**
     * 删除卡券接口.
     *
     * @param $cardId
     *
     * @return array
     */
    public function cardDelete($cardId)
    {
        $params = [
            'card_id' => $cardId,
        ];

        return $this->parseJSON('json', [self::API_CARD_DELETE, $params]);
    }

    /**
     * 设置卡券失效.
     *
     * @param      $code
     * @param null $cardId
     *
     * @return array
     */
    public function unavailable($code, $cardId = null)
    {
        $params = [
            'code' => $code,
            'card_id' => $cardId,
        ];

        return $this->parseJSON('json', [self::API_UNAVAILABLE, $params]);
    }

    /**
     * 拉取卡券概况数据接口.
     *
     * @param     $beginDate
     * @param     $endDate
     * @param int $condSource
     *
     * @return array
     */
    public function getCardBizUinInfo($beginDate, $endDate, $condSource = 0)
    {
        if (is_numeric($beginDate)) {
            $beginDate = date('Y-m-d', $beginDate);
        }

        if (is_numeric($endDate)) {
            $endDate = date('Y-m-d', $endDate);
        }

        $params = [
            'begin_date' => $beginDate,
            'end_date' => $endDate,
            'cond_source' => intval($condSource),
        ];

        return $this->parseJSON('json', [self::API_CARD_BIZ_UIN_INFO, $params]);
    }

    /**
     * 获取免费券数据接口.
     *
     * @param        $beginDate
     * @param        $endDate
     * @param int    $condSource
     * @param string $cardId
     *
     * @return array
     */
    public function getCardCardInfo($beginDate, $endDate, $condSource = 0, $cardId = '')
    {
        $params = [
            'begin_date' => $beginDate,
            'end_date' => $endDate,
            'cond_source' => intval($condSource),
            'card_id' => $cardId,
        ];

        return $this->parseJSON('json', [self::API_CARD_CARD_INFO, $params]);
    }

    /**
     * 拉取会员卡数据接口.
     *
     * @param     $beginDate
     * @param     $endDate
     * @param int $condSource
     *
     * @return array
     */
    public function getCardMemberCardInfo($beginDate, $endDate, $condSource = 0)
    {
        $params = [
            'begin_date' => $beginDate,
            'end_date' => $endDate,
            'cond_source' => intval($condSource),
        ];

        return $this->parseJSON('json', [self::API_CARD_MEMBER_CARD_INFO, $params]);
    }

    /**
     * 会员卡接口激活.
     *
     * @param array $activate
     *
     * @return array
     */
    public function activate($activate = [])
    {
        return $this->parseJSON('json', [self::API_CARD_ACTIVATE, $activate]);
    }

    /**
     * 设置开卡字段接口.
     *
     * @param       $cardId
     * @param array $requiredForm
     * @param array $optionalForm
     *
     * @return array
     */
    public function activateUserFrom($cardId, $requiredForm = [], $optionalForm = [])
    {
        $card = [];
        $card['card_id'] = $cardId;

        $params = array_merge($card, $requiredForm, $optionalForm);

        return $this->parseJSON('json', [self::API_ACTIVATE_USER_FORM, $params]);
    }

    /**
     * 拉取会员信息接口.
     *
     * @param $cardId
     * @param $code
     *
     * @return array
     */
    public function memberCardUserInfo($cardId, $code)
    {
        $params = [
            'card_id' => $cardId,
            'code' => $code,
        ];

        return $this->parseJSON('json', [self::API_MEMBER_USER_INFO, $params]);
    }

    /**
     * 更新会员信息.
     *
     * @param array $updateUser
     *
     * @return array
     */
    public function memberCardUpdateUser($updateUser = [])
    {
        $params = $updateUser;

        return $this->parseJSON('json', [self::API_UPDATE_USER, $params]);
    }

    /**
     * 添加子商户.
     *
     * @param        $brandName
     * @param        $logoUrl
     * @param        $protocol
     * @param        $endTime
     * @param        $primaryCategoryId
     * @param        $secondaryCategoryId
     * @param        $agreementMediaId
     * @param        $operatorMediaId
     * @param string $appId
     *
     * @return array
     */
    public function subMerchant($brandName, $logoUrl, $protocol, $endTime, $primaryCategoryId, $secondaryCategoryId, $agreementMediaId, $operatorMediaId, $appId = '')
    {
        $params = [
            'info' => [
                'brand_name' => $brandName,
                'logo_url' => $logoUrl,
                'protocol' => $protocol,
                'end_time' => $endTime,
                'primary_category_id' => $primaryCategoryId,
                'secondary_category_id' => $secondaryCategoryId,
                'agreement_media_id' => $agreementMediaId,
                'operator_media_id' => $operatorMediaId,
                'app_id' => $appId,
            ],
        ];

        return $this->parseJSON('json', [self::API_SUB_MERCHANT, $params]);
    }

    /**
     * 卡券开放类目查询接口.
     *
     * @return array|bool
     */
    public function getApplyProtocol()
    {
        return $this->parseJSON('get', [self::API_GET_APPLY_PROTOCOL]);
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
     * Set current url.
     *
     * @param string $url
     *
     * @return array
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
}
