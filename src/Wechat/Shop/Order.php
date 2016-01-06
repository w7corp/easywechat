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
 * Order.php.
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    a939638621 <a939638621@hotmail.com>
 * @copyright 2015 a939638621 <a939638621@hotmail.com>
 *
 * @link      https://github.com/a939638621
 */

namespace Overtrue\Wechat\Shop;

use Overtrue\Wechat\Shop\Foundation\Base;
use Overtrue\Wechat\Shop\Foundation\Order as OrderInterface;
use Overtrue\Wechat\Shop\Foundation\ShopsException;

/**
 * 订单管理.
 *
 * Class Order
 */
class Order extends Base implements OrderInterface
{
    /**
     * 快递.
     */
    const EMS = 'Fsearch_code';
    const STO = '002shentong';
    const ZTO = '066zhongtong';
    const YTO = '056yuantong';
    const TTK = '042tiantian';
    const SF = '003shunfeng';
    const YUN_DA = '059Yunda';
    const ZJS = '064zhaijisong';
    const HUI_TONG = '020huitong';
    const YI_XUN = 'zj001yixun';

    const API_GET_BY_ID = 'https://api.weixin.qq.com/merchant/order/getbyid';
    const API_GET_BY_ATTRIBUTE = 'https://api.weixin.qq.com/merchant/order/getbyfilter';
    const API_SET_DELIVERY = 'https://api.weixin.qq.com/merchant/order/setdelivery';
    const API_CLOSE = 'https://api.weixin.qq.com/merchant/order/close';

    /**
     * 根据订单ID获取订单详情.
     *
     * @param $orderId
     *
     * @return array
     *
     * @throws ShopsException
     */
    public function getById($orderId)
    {
        $this->response = $this->http->jsonPost(self::API_GET_BY_ID, array('order_id' => $orderId));

        return $this->getResponse();
    }

    /**
     * 根据订单状态/创建时间获取订单详情.
     *
     * @param null $status
     * @param null $beginTime
     * @param null $endTime
     *
     * @return mixed
     *
     * @throws ShopsException
     */
    public function getByAttribute($status = null, $beginTime = null, $endTime = null)
    {
        $data = array();

        if (!empty($status)) {
            $data['status'] = $status;
        }
        if (!empty($beginTime)) {
            $data['begintime'] = $beginTime;
        }
        if (!empty($endTime)) {
            $data['endtime'] = $endTime;
        }

        $this->response = $this->http->jsonPost(self::API_GET_BY_ATTRIBUTE, $data);

        return $this->getResponse();
    }

    /**
     * 设置发货信息.
     *
     * @param $orderId
     * @param string $deliveryCompany
     * @param string $deliveryTrackNo
     * @param int    $isOthers
     *
     * @return bool
     *
     * @throws ShopsException
     */
    public function setDelivery($orderId, $deliveryCompany = null, $deliveryTrackNo = null, $isOthers = 0)
    {
        $data = array(
            'order_id' => $orderId,
        );

        $data['is_others'] = $isOthers;

        if (empty($deliveryCompany) && empty($deliveryTrackNo)) {
            $data['need_delivery'] = 0;
        } else {
            $data['need_delivery'] = 1;
            $data['delivery_company'] = $deliveryCompany;
            $data['delivery_track_no'] = $deliveryTrackNo;
        }

        $this->response = $this->http->jsonPost(self::API_SET_DELIVERY, $data);

        return $this->getResponse();
    }

    /**
     * 关闭订单.
     *
     * @param $orderId
     *
     * @return bool
     *
     * @throws ShopsException
     */
    public function close($orderId)
    {
        $this->response = $this->http->jsonPost(self::API_CLOSE, array('order_id' => $orderId));

        return $this->getResponse();
    }
}
