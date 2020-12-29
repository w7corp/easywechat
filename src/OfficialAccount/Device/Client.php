<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\Device;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @see http://iot.weixin.qq.com/wiki/new/index.html
 *
 * @author soone <66812590@qq.com>
 */
class Client extends BaseClient
{
    /**
     * @param string $deviceId
     * @param string $openid
     * @param string $content
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function message(string $deviceId, string $openid, string $content)
    {
        $params = [
            'device_type' => $this->app['config']['device_type'],
            'device_id' => $deviceId,
            'open_id' => $openid,
            'content' => base64_encode($content),
        ];

        return $this->httpPostJson('device/transmsg', $params);
    }

    /**
     * Get device qrcode.
     *
     * @param array $deviceIds
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function qrCode(array $deviceIds)
    {
        $params = [
            'device_num' => count($deviceIds),
            'device_id_list' => $deviceIds,
        ];

        return $this->httpPostJson('device/create_qrcode', $params);
    }

    /**
     * @param array  $devices
     * @param string $productId
     * @param int    $opType
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function authorize(array $devices, string $productId, int $opType = 0)
    {
        $params = [
            'device_num' => count($devices),
            'device_list' => $devices,
            'op_type' => $opType,
            'product_id' => $productId,
        ];

        return $this->httpPostJson('device/authorize_device', $params);
    }

    /**
     * 获取 device id 和二维码
     *
     * @param string $productId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function createId(string $productId)
    {
        $params = [
            'product_id' => $productId,
        ];

        return $this->httpGet('device/getqrcode', $params);
    }

    /**
     * @param string $openid
     * @param string $deviceId
     * @param string $ticket
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function bind(string $openid, string $deviceId, string $ticket)
    {
        $params = [
            'ticket' => $ticket,
            'device_id' => $deviceId,
            'openid' => $openid,
        ];

        return $this->httpPostJson('device/bind', $params);
    }

    /**
     * @param string $openid
     * @param string $deviceId
     * @param string $ticket
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unbind(string $openid, string $deviceId, string $ticket)
    {
        $params = [
            'ticket' => $ticket,
            'device_id' => $deviceId,
            'openid' => $openid,
        ];

        return $this->httpPostJson('device/unbind', $params);
    }

    /**
     * @param string $openid
     * @param string $deviceId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function forceBind(string $openid, string $deviceId)
    {
        $params = [
            'device_id' => $deviceId,
            'openid' => $openid,
        ];

        return $this->httpPostJson('device/compel_bind', $params);
    }

    /**
     * @param string $openid
     * @param string $deviceId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function forceUnbind(string $openid, string $deviceId)
    {
        $params = [
            'device_id' => $deviceId,
            'openid' => $openid,
        ];

        return $this->httpPostJson('device/compel_unbind', $params);
    }

    /**
     * @param string $deviceId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function status(string $deviceId)
    {
        $params = [
            'device_id' => $deviceId,
        ];

        return $this->httpGet('device/get_stat', $params);
    }

    /**
     * @param string $ticket
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function verify(string $ticket)
    {
        $params = [
            'ticket' => $ticket,
        ];

        return $this->httpPost('device/verify_qrcode', $params);
    }

    /**
     * @param string $deviceId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function openid(string $deviceId)
    {
        $params = [
            'device_type' => $this->app['config']['device_type'],
            'device_id' => $deviceId,
        ];

        return $this->httpGet('device/get_openid', $params);
    }

    /**
     * @param string $openid
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function listByOpenid(string $openid)
    {
        $params = [
            'openid' => $openid,
        ];

        return $this->httpGet('device/get_bind_device', $params);
    }
}
