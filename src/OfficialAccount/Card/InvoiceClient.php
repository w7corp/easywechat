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
 * Class InvoiceClient.
 *
 * @author overtrue <i@overtrue.me>
 */
class InvoiceClient extends BaseClient
{
    /**
     * 设置支付后开票信息接口.
     *
     * @param string $mchid
     * @param string $sPappid
     *
     * @return mixed
     */
    public function set(string $mchid, string $sPappid)
    {
        $params = [
            'paymch_info' => [
                'mchid' => $mchid,
                's_pappid' => $sPappid,
            ],
        ];

        return $this->setBizAttr('set_pay_mch', $params);
    }

    /**
     * 查询支付后开票信息接口.
     *
     * @return mixed
     */
    public function get()
    {
        return $this->setBizAttr('get_pay_mch');
    }

    /**
     * 设置授权页字段信息接口.
     *
     * @param array $userData
     * @param array $bizData
     *
     * @return mixed
     */
    public function setAuthField(array $userData, array $bizData)
    {
        $params = [
            'auth_field' => [
                'user_field' => $userData,
                'biz_field' => $bizData,
            ],
        ];

        return $this->setBizAttr('set_auth_field', $params);
    }

    /**
     * 查询授权页字段信息接口.
     *
     * @return mixed
     */
    public function getAuthField()
    {
        return $this->setBizAttr('get_auth_field');
    }

    /**
     * 查询开票信息.
     *
     * @param string $orderId
     * @param string $appId
     *
     * @return mixed
     */
    public function getAuthData(string $appId, string $orderId)
    {
        $params = [
            'order_id' => $orderId,
            's_appid' => $appId,
        ];

        return $this->httpPost('card/invoice/getauthdata', $params);
    }

    /**
     * @param string $action
     * @param array  $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    private function setBizAttr(string $action, array $params = [])
    {
        return $this->httpPostJson('card/invoice/setbizattr', $params, ['action' => $action]);
    }
}
