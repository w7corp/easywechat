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
 * @author soone <66812590@qq.com>
 */
class Client extends BaseClient
{
    /**
     * @var string
     */
    protected $productId;

    /**
     * Client constructor.
     *
     * @param \Pimple\Container $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->productId = $this->app['config']['product_id'];
    }

    /**
     * Set product id.
     *
     * @param string $productId
     *
     * @return $this
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Send message to device.
     *
     * @param $deviceId
     * @param $openId
     * @param $content
     *
     * @return mixed
     */
    public function sendToDevice($deviceId, $openId, $content)
    {
        $params = [
            'device_type' => $this->app['config']['device_type'],
            'device_id' => $deviceId,
            'open_id' => $openId,
            'content' => base64_decode($content, true),
        ];

        return $this->httpPostJson('device/transmsg', $params);
    }

    /**
     * Get device qrcode.
     *
     * @param array $deviceIds
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getDeviceQrcode(array $deviceIds)
    {
        $params = [
            'device_num' => count($deviceIds),
            'device_id_list' => $deviceIds,
        ];

        return $this->httpPostJson('device/create_qrcode', $params);
    }

    /**
     * @param array $deviceList
     * @param int   $opType
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function authorizeDevice(array $deviceList, $opType = 0)
    {
        $params = [
            'device_num' => count($deviceList),
            'device_list' => $this->getDeviceList($deviceList),
            'op_type' => $opType,
            'product_id' => $this->productId,
        ];

        return $this->httpPostJson('device/authorize_device', $params);
    }

    /**
     * @param array $deviceList
     *
     * @return array
     */
    protected function getDeviceList($deviceList)
    {
        return array_map(function ($info) {
            $item = [
                'id' => $info['deviceId'],
                'mac' => $info['mac'],
                'connect_protocol' => $this->app['config']['connect_protocol'],
                'auth_key' => $this->app['config']['auth_key'],
                'close_strategy' => $this->app['config']['close_strategy'],
                'conn_strategy' => $this->app['config']['conn_strategy'],
                'crypt_method' => $this->app['config']['crypt_method'],
                'auth_ver' => $this->app['config']['auth_ver'],
                'manu_mac_pos' => $this->app['config']['manu_mac_pos'],
                'ser_mac_pos' => $this->app['config']['ser_mac_pos'],
            ];
            if ($protocol = $this->app['config']['ble_simple_protocol']) {
                $item['ble_simple_protocol'] = $protocol;
            }

            return $item;
        }, $deviceList);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createDeviceId()
    {
        $params = [
            'product_id' => $this->productId,
        ];

        return $this->httpGet('device/getqrcode', $params);
    }

    /**
     * @param string $openId
     * @param string $deviceId
     * @param string $ticket
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function bind($openId, $deviceId, $ticket)
    {
        $params = [
            'ticket' => $ticket,
            'device_id' => $deviceId,
            'openid' => $openId,
        ];

        return $this->httpPostJson('device/bind', $params);
    }

    /**
     * @param string $openId
     * @param string $deviceId
     * @param string $ticket
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function unbind($openId, $deviceId, $ticket)
    {
        $params = [
            'ticket' => $ticket,
            'device_id' => $deviceId,
            'openid' => $openId,
        ];

        return $this->httpPostJson('device/unbind', $params);
    }

    /**
     * @param string $openId
     * @param string $deviceId
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function compelBind($openId, $deviceId)
    {
        $params = [
            'device_id' => $deviceId,
            'openid' => $openId,
        ];

        return $this->httpPostJson('device/compel_bind', $params);
    }

    /**
     * @param string $openId
     * @param string $deviceId
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function compelUnbind($openId, $deviceId)
    {
        $params = [
            'device_id' => $deviceId,
            'openid' => $openId,
        ];

        return $this->httpPostJson('device/compel_unbind', $params);
    }

    /**
     * @param string $deviceId
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getDeviceStatus($deviceId)
    {
        $params = [
            'device_id' => $deviceId,
        ];

        return $this->httpGet('device/get_stat', $params);
    }

    /**
     * @param string $ticket
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function verifyQrcode($ticket)
    {
        $params = [
            'ticket' => $ticket,
        ];

        return $this->httpPost('device/verify_qrcode', $params);
    }

    /**
     * @param string $deviceId
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getOpenid($deviceId)
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
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getDeviceIdByOpenid($openid)
    {
        $params = [
            'openid' => $openid,
        ];

        return $this->httpGet('device/get_bind_device', $params);
    }
}
