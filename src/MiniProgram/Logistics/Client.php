<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MiniProgram\Logistics;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author kehuanhuan <1152018701@qq.com>
 */
class Client extends BaseClient
{
    /**
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function getAllExpress()
    {
        return $this->httpGet('cgi-bin/express/business/delivery/getall');
    }

    /**
     * @param array $data
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function addOrder(array $data = [])
    {
        return $this->httpPostJson('cgi-bin/express/business/order/add', $data);
    }

    /**
     * @param array $data
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function cancelOrder(array $data = [])
    {
        return $this->httpPostJson('cgi-bin/express/business/order/cancel', $data);
    }

    /**
     * @param array $data
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function getOrder(array $data = [])
    {
        return $this->httpPostJson('cgi-bin/express/business/order/get', $data);
    }
  
    /**
     * @param array $data
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function getPath(array $data = [])
    {
        return $this->httpPostJson('cgi-bin/express/business/path/get', $data);
    }
}
