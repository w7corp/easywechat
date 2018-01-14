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
    protected $ticketCachePrefix = 'easywechat.official_account.card.api_ticket.';

    /**
     * 获取卡券颜色.
     *
     * @return mixed
     */
    public function colors()
    {
        return $this->httpGet('card/getcolors');
    }

    /**
     * 卡券开放类目查询接口.
     *
     * @return mixed
     */
    public function categories()
    {
        return $this->httpGet('card/getapplyprotocol');
    }

    /**
     * 创建卡券.
     *
     * @param string $cardType
     * @param array  $attributes
     *
     * @return mixed
     */
    public function create($cardType = 'member_card', array $attributes)
    {
        $params = [
            'card' => [
                'card_type' => strtoupper($cardType),
                strtolower($cardType) => $attributes,
            ],
        ];

        return $this->httpPostJson('card/create', $params);
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
    public function list($offset = 0, $count = 10, $statusList = 'CARD_STATUS_VERIFY_OK')
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
     * @param array  $attributes
     *
     * @return mixed
     */
    public function update($cardId, $type, array $attributes = [])
    {
        $card = [];
        $card['card_id'] = $cardId;
        $card[strtolower($type)] = $attributes;

        return $this->httpPostJson('card/update', $card);
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
     * 创建二维码.
     *
     * @param array $cards
     *
     * @return mixed
     */
    public function createQrCode(array $cards)
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
    public function getQrCode($ticket)
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
    public function setTestWhitelistByName(array $usernames)
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
        $key = 'increase' === $action ? 'increase_stock_value' : 'reduce_stock_value';
        $params = [
            'card_id' => $cardId,
            $key => abs($amount),
        ];

        return $this->httpPostJson('card/modifystock', $params);
    }
}
