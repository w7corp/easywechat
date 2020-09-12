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

/**
 * Class GiftCardPageClient.
 *
 * @author overtrue <i@overtrue.me>
 */
class GiftCardPageClient extends BaseClient
{
    /**
     * 创建-礼品卡货架接口.
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function add(array $attributes)
    {
        $params = [
            'page' => $attributes,
        ];

        return $this->httpPostJson('card/giftcard/page/add', $params);
    }

    /**
     * 查询-礼品卡货架信息接口.
     *
     * @param string $pageId
     *
     * @return mixed
     */
    public function get(string $pageId)
    {
        $params = [
            'page_id' => $pageId,
        ];

        return $this->httpPostJson('card/giftcard/page/get', $params);
    }

    /**
     * 修改-礼品卡货架信息接口.
     *
     * @param string $pageId
     * @param string $bannerPicUrl
     * @param array  $themeList
     *
     * @return mixed
     */
    public function update(string $pageId, string $bannerPicUrl, array $themeList)
    {
        $params = [
            'page' => [
                'page_id' => $pageId,
                'banner_pic_url' => $bannerPicUrl,
                'theme_list' => $themeList,
            ],
        ];

        return $this->httpPostJson('card/giftcard/page/update', $params);
    }

    /**
     * 查询-礼品卡货架列表接口.
     *
     * @return mixed
     */
    public function list()
    {
        return $this->httpPostJson('card/giftcard/page/batchget');
    }

    /**
     * 下架-礼品卡货架接口(下架某一个货架或者全部货架).
     *
     * @param string $pageId
     *
     * @return mixed
     */
    public function setMaintain(string $pageId = '')
    {
        $params = ($pageId ? ['page_id' => $pageId] : ['all' => true]) + [
                'maintain' => true,
            ];

        return $this->httpPostJson('card/giftcard/maintain/set', $params);
    }
}
