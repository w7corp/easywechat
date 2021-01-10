<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Card;

use EasyWeChat\Kernel\BaseClient;

class GiftCardClient extends BaseClient
{
    /**
     * 申请微信支付礼品卡权限接口.
     *
     * @param string $subMchId
     *
     * @return mixed
     */
    public function add(string $subMchId)
    {
        $params = [
            'sub_mch_id' => $subMchId,
        ];

        return $this->httpPostJson('card/giftcard/pay/whitelist/add', $params);
    }

    /**
     * 绑定商户号到礼品卡小程序接口(商户号必须为公众号申请的商户号，否则报错).
     *
     * @param string $subMchId
     * @param string $wxaAppid
     *
     * @return mixed
     */
    public function bind(string $subMchId, string $wxaAppid)
    {
        $params = [
            'sub_mch_id' => $subMchId,
            'wxa_appid' => $wxaAppid,
        ];

        return $this->httpPostJson('card/giftcard/pay/submch/bind', $params);
    }

    /**
     * 上传小程序代码.
     *
     * @param string $wxaAppid
     * @param string $pageId
     *
     * @return mixed
     */
    public function set(string $wxaAppid, string $pageId)
    {
        $params = [
            'wxa_appid' => $wxaAppid,
            'page_id' => $pageId,
        ];

        return $this->httpPostJson('card/giftcard/wxa/set', $params);
    }
}
