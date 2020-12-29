<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Payment\Coupon;

use EasyWeChat\Payment\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author tianyong90 <412039588@qq.com>
 */
class Client extends BaseClient
{
    /**
     * send a cash coupon.
     *
     * @param array $params
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(array $params)
    {
        $params['appid'] = $this->app['config']->app_id;
        $params['openid_count'] = 1;

        return $this->safeRequest('mmpaymkttransfers/send_coupon', $params);
    }

    /**
     * query a coupon stock.
     *
     * @param array $params
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function stock(array $params)
    {
        $params['appid'] = $this->app['config']->app_id;

        return $this->request('mmpaymkttransfers/query_coupon_stock', $params);
    }

    /**
     * query a info of coupon.
     *
     * @param array $params
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function info(array $params)
    {
        $params['appid'] = $this->app['config']->app_id;

        return $this->request('mmpaymkttransfers/querycouponsinfo', $params);
    }
}
