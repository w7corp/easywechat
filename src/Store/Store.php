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
 * Store.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */
namespace EasyWeChat\Store;

use EasyWeChat\Core\AbstractAPI;

/**
 * Class stats.
 */
class Store extends AbstractAPI
{
	const API_GET = 'https://api.weixin.qq.com/merchant/get';
	const API_GET_BY_STATUS = 'https://api.weixin.qq.com/merchant/getbystatus';
	const API_GROUP_GETALL="https://api.weixin.qq.com/merchant/group/getall";
	const API_GROUP_GET_BY_ID="https://api.weixin.qq.com/merchant/group/getbyid";
	const API_ORDER_GET_BY_ID="https://api.weixin.qq.com/merchant/order/getbyid";

	/**
	 * Get product info.
	 *
	 * @return \EasyWeChat\Support\Collection
	 */
	public function get($productId)
	{
		return $this->parseJSON('json', [self::API_GET, ['product_id'=>$productId]]);
	}

	/**
	 * Get product list.
	 *
	 * @return \EasyWeChat\Support\Collection
	 */
	public function getByStatus($status = 0)
	{
		return $this->parseJSON('json', [self::API_GET_BY_STATUS, ['status'=>$status]]);
	}

	/**
	 * Get product group list
	 *
	 * @return \EasyWeChat\Support\Collection
	 */
	public function getAllGroup()
	{
		return $this->parseJSON('get', [self::API_GROUP_GETALL]);
	}

	public function getGroupById($groupId)
	{
		return $this->parseJSON('json', [self::API_GROUP_GET_BY_ID, ['group_id'=>$groupId]]);
	}

	public function getOrderById($orderId)
	{
		return $this->parseJSON('json', [self::API_ORDER_GET_BY_ID, ['order_id'=>$orderId]]);
	}
}