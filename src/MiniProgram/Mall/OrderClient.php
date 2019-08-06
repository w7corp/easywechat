<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MiniProgram\Mall;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class OrderClient extends BaseClient
{
    /**
     * 导入订单.
     *
     * @param array $params
     * @param bool  $isHistory
     */
    public function add($params, $isHistory = false)
    {
        return $this->httpPostJson('mall/importorder', $params, ['action' => 'add-order', 'is_history' => (int) $isHistory]);
    }

    /**
     * 导入订单.
     *
     * @param array $params
     * @param bool  $isHistory
     *
     * @return mixed
     */
    public function update($params, $isHistory = false)
    {
        return $this->httpPostJson('mall/importorder', $params, ['action' => 'update-order', 'is_history' => (int) $isHistory]);
    }

    /**
     * 删除订单.
     *
     * @param string $openid
     * @param string $orderId
     *
     * @return mixed
     */
    public function delete($openid, $orderId)
    {
        $params = [
            'user_open_id' => $openid,
            'order_id' => $orderId,
        ];

        return $this->httpPostJson('mall/deleteorder', $params);
    }
}
