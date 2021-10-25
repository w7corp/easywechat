<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MiniProgram\Union;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author Abbotton <uctoo@foxmail.com>
 */
class Client extends BaseClient
{
    /**
     * Add promotion.
     *
     * @param string $promotionSourceName
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createPromotion(string $promotionSourceName)
    {
        $params = [
            'promotionSourceName' => $promotionSourceName,
        ];

        return $this->httpPostJson('union/promoter/promotion/add', $params);
    }

    /**
     * Delete promotion.
     *
     * @param string $promotionSourcePid
     * @param string $promotionSourceName
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deletePromotion(string $promotionSourcePid, string $promotionSourceName)
    {
        $params = [
            'promotionSourceName' => $promotionSourceName,
            'promotionSourcePid' => $promotionSourcePid,
        ];

        return $this->httpPostJson('union/promoter/promotion/del', $params);
    }

    /**
     * Update promotion.
     *
     * @param array $previousPromotionInfo
     * @param array $promotionInfo
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updatePromotion(array $previousPromotionInfo, array $promotionInfo)
    {
        $params = [
            'previousPromotionInfo' => $previousPromotionInfo,
            'promotionInfo' => $promotionInfo,
        ];

        return $this->httpPostJson('union/promoter/promotion/upd', $params);
    }

    /**
     * Get a list of promotion spots.
     *
     * @param int $start
     * @param int $limit
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPromotionSourceList(int $start = 0, int $limit = 20)
    {
        $params = [
            'start' => $start,
            'limit' => $limit
        ];

        return $this->httpGet('union/promoter/promotion/list', $params);
    }

    /**
     * Get the list of affiliate product categories and category IDs.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getProductCategory()
    {
        return $this->httpGet('union/promoter/product/category');
    }

    /**
     * Get the list and detail of affiliate product.
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getProductList(array $params)
    {
        return $this->httpGet('union/promoter/product/list', $params);
    }

    /**
     * Get product promotion materials
     *
     * @param string $pid
     * @param array $productList
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getProductMaterial(string $pid, array $productList)
    {
        $params = [
            'pid' => $pid,
            'productList' => $productList,
        ];

        return $this->httpPostJson('union/promoter/product/generate', $params);
    }

    /**
     * Query order details based on order ID array.
     *
     * @param array $orderIdList
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getOrderInfo(array $orderIdList)
    {
        return $this->httpPostJson('union/promoter/order/info', $orderIdList);
    }

    /**
     * Query and filter the order list.
     *
     * @param int $page
     * @param string $startTimestamp
     * @param string $endTimestamp
     * @param string $commissionStatus
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function searchOrder(int $page = 1, $startTimestamp = '', $endTimestamp = '', $commissionStatus = '')
    {
        $params = [
            'page' => $page,
            'startTimestamp' => $startTimestamp,
            'endTimestamp' => $endTimestamp,
            'commissionStatus' => $commissionStatus
        ];

        return $this->httpGet('union/promoter/order/search', $params);
    }

    /**
     * Get featured products of union.
     *
     * @param  array  $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getFeaturedProducts(array $params)
    {
        return $this->httpGet('union/promoter/product/select', $params);
    }

    /**
     * Query the details of the targeted plan.
     *
     * @param  array  $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTargetPlanInfo(array $params)
    {
        return $this->httpGet('union/promoter/target/plan_info', $params);
    }

    /**
     * Apply to join the targeted plan.
     *
     * @param  array  $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function applyJoinTargetPlan(array $params)
    {
        return $this->httpPostJson('union/promoter/target/apply_target', $params);
    }

    /**
     * Query the status of the targeted plan apply.
     *
     * @param  array  $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTargetPlanStatus(array $params)
    {
        return $this->httpGet('union/promoter/target/apply_status', $params);
    }
}
