<?php

namespace EasyWeChat\MiniProgram\Transactions\Coupon;

use EasyWeChat\MiniProgram\Core\AbstractMiniProgram;

class Coupon extends AbstractMiniProgram
{

    const API_POST_SHOP_COUPON_CONFIRM = 'https://api.weixin.qq.com/shop/coupon/confirm';
    const API_POST_SHOP_COUPON_ADD = 'https://api.weixin.qq.com/shop/coupon/add';
    const API_POST_SHOP_COUPON_ADD_USER = 'https://api.weixin.qq.com/shop/coupon/add_user_coupon';
    const API_POST_SHOP_COUPON_USER_LIST = 'https://api.weixin.qq.com/shop/coupon/get_usercoupon_list';
    const API_POST_SHOP_COUPON_UPDATE = 'https://api.weixin.qq.com/shop/coupon/update';
    const API_POST_SHOP_COUPON_UPDATE_USER = 'https://api.weixin.qq.com/shop/coupon/update_user_coupon';
    const API_POST_SHOP_COUPON_UPDATE_USER_STATUS = 'https://api.weixin.qq.com/shop/coupon/update_usercoupon_status';
    const API_POST_SHOP_COUPON_UPDATE_STOCK = 'https://api.weixin.qq.com/shop/coupon/update_coupon_stock';
    const API_POST_SHOP_COUPON_UPDATE_STATUS = 'https://api.weixin.qq.com/shop/coupon/update_status';
    const API_POST_SHOP_COUPON_GET = 'https://api.weixin.qq.com/shop/coupon/get';
    const API_POST_SHOP_COUPON_GET_LIST = 'https://api.weixin.qq.com/shop/coupon/get_list';

    /**商家确认回调领券事件
     * @return \Psr\Http\Message\StreamInterface
     */
    public function couponConfirm()
    {
        return $this->getStream(self::API_POST_SHOP_COUPON_CONFIRM, []);
    }

    /**商家确认回调领券事件
     * @return \Psr\Http\Message\StreamInterface
     */
    public function addCoupon(array $params)
    {
        return $this->getStream(self::API_POST_SHOP_COUPON_ADD, $params);
    }

    /**添加用户优惠券
     * @return \Psr\Http\Message\StreamInterface
     */
    public function addUserCoupon(array $params)
    {
        return $this->getStream(self::API_POST_SHOP_COUPON_ADD_USER, $params);
    }

    /**获取用户优惠券列表
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getUserCoupons(string $openId, int $pageSize, int $offset)
    {
        $params = ['page_size' => $pageSize, 'offset' => $offset, 'openid' => $openId];

        return $this->getStream(self::API_POST_SHOP_COUPON_USER_LIST, $params);
    }

    /**获取用户优惠券列表
     * @return \Psr\Http\Message\StreamInterface
     */
    public function updateUserCouponStatus(string $openId, string $outCouponId, string $outUserCouponId, int $status)
    {
        $params = ['openid' => $openId, 'out_coupon_id' => $outCouponId, 'out_user_coupon_id' => $openId, 'status' => $status];
        return $this->getStream(self::API_POST_SHOP_COUPON_UPDATE_USER_STATUS, $params);
    }

    /**更新用户优惠券
     * @param array $params
     * @return \Psr\Http\Message\StreamInterface
     */
    public function updateUserCoupon(array $params)
    {
        return $this->getStream(self::API_POST_SHOP_COUPON_UPDATE_USER, $params);

    }

    /**更新优惠券
     * @param array $params
     * @return \Psr\Http\Message\StreamInterface
     */
    public function updateCoupon(array $params)
    {
        return $this->getStream(self::API_POST_SHOP_COUPON_UPDATE, $params);
    }

    /** 更新优惠券库存
     * @param array $params
     * @return \Psr\Http\Message\StreamInterface
     */
    public function updateCouponStock(array $params)
    {
        return $this->getStream(self::API_POST_SHOP_COUPON_UPDATE_STOCK, $params);
    }

    /**更新优惠券状态
     * @param string $outCouponId
     * @param int $status
     * @return \Psr\Http\Message\StreamInterface
     */
    public function updateCouponStatus(string $outCouponId, int $status)
    {
        $params = ['out_coupon_id' => $outCouponId, 'status' => $status];
        return $this->getStream(self::API_POST_SHOP_COUPON_UPDATE_STATUS, $params);
    }

    /** 获取优惠券详情
     * @param string $outCouponId
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getCoupon(string $outCouponId)
    {
        $params = ['out_coupon_id' => $outCouponId];
        return $this->getStream(self::API_POST_SHOP_COUPON_GET, $params);
    }

    /** 获取优惠券详情
     * @param string $outCouponId
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getCoupons(int $pageSize, int $offset)
    {
        $params = ['page_size' => $pageSize, 'offset' => $offset];
        return $this->getStream(self::API_POST_SHOP_COUPON_GET_LIST, $params);
    }

    /**
     * Get stream.
     *
     * @param string $endpoint
     * @param array $params
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    protected function getStream($endpoint, $params)
    {
        return json_decode(strval($this->getHttp()
            ->json($endpoint, $params)->getBody()), true);
    }
}