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

namespace Overtrue\Wechat\Shop\Foundation;

/**
 * 订单管理.
 *
 * Interface Order
 */
interface Order
{
    /**
     * 根据订单ID获取订单详情.
     *
     * @param $orderId
     *
     * @return array
     */
    public function getById($orderId);

    /**
     * 根据订单状态/创建时间获取订单详情.
     *
     * @param null $status
     * @param null $beginTime
     * @param null $endTime
     *
     * @return array
     */
    public function getByAttribute($status = null, $beginTime = null, $endTime = null);

    /**
     * 设置发货信息.
     *
     * @param $orderId
     * @param string $deliveryCompany
     * @param string $deliveryTrackNo
     * @param int    $isOthers
     *
     * @return bool
     */
    public function setDelivery($orderId, $deliveryCompany = null, $deliveryTrackNo = null, $isOthers = 0);

    /**
     * 关闭订单.
     *
     * @param $orderId
     *
     * @return bool
     */
    public function close($orderId);
}
