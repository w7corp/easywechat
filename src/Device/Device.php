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
 * Device.php.
 *
 * @author    soone <66812590@qq.com>
 * @copyright 2016 soone <66812590@qq.com>
 */

namespace EasyWeChat\Device;

use EasyWeChat\Core\AbstractAPI;
use EasyWeChat\Core\AccessToken;

/**
 * Class Device.
 */
class Device extends AbstractAPI
{
    protected $deviceType;

    protected $productId;

    protected $config;

    const API_TRANS_MSG = 'https://api.weixin.qq.com/device/transmsg';
    const API_CREATE = 'https://api.weixin.qq.com/device/create_qrcode';
    const API_DEV_STAT = 'https://api.weixin.qq.com/device/get_stat';
    const API_DEV_AUTH = 'https://api.weixin.qq.com/device/authorize_device';
    const API_DEV_GET_QRCODE = 'https://api.weixin.qq.com/device/getqrcode';
    const API_DEV_VERIFY_QRCODE = 'https://api.weixin.qq.com/device/verify_qrcode';
    const API_DEV_BIND = 'https://api.weixin.qq.com/device/bind';
    const API_DEV_UNBIND = 'https://api.weixin.qq.com/device/unbind';
    const API_DEV_COMPEL_BIND = 'https://api.weixin.qq.com/device/compel_bind';
    const API_DEV_COMPEL_UNBIND = 'https://api.weixin.qq.com/device/compel_unbind';
    const API_DEV_GET_OPENID = 'https://api.weixin.qq.com/device/get_openid';
    const API_USER_DEV_BIND = 'https://api.weixin.qq.com/device/get_bind_device';

    public function __construct(AccessToken $accessToken, $config)
    {
        parent::setAccessToken($accessToken);
        $this->config = $config;
        $this->deviceType = $this->config['device_type'];
        $this->productId = $this->config['product_id'];
    }

    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Send message to device.
     *
     * @param int $sceneValue
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function sendToDevice($deviceId, $openId, $content)
    {
        $params = [
            'device_type' => $this->deviceType,
            'device_id' => $deviceId,
            'open_id' => $openId,
            'content' => base64_decode($content, true),
        ];

        return $this->parseJSON('json', [self::API_TRANS_MSG, $params]);
    }

    public function getDeviceQrcode(array $deviceIds)
    {
        $params = [
            'device_num' => count($deviceIds),
            'device_id_list' => $deviceIds,
        ];

        return $this->parseJSON('json', [self::API_CREATE, $params]);
    }

    public function authorizeDevice(array $deviceInfos, $opType = 0)
    {
        $params = [
            'device_num' => count($deviceInfos),
            'device_list' => $this->getDeviceList($deviceInfos),
            'op_type' => $opType,
            'product_id' => $this->productId,
        ];

        return $this->parseJSON('json', [self::API_DEV_AUTH, $params]);
    }

    protected function getDeviceList($deviceInfos)
    {
        $res = [];
        foreach ($deviceInfos as $dInfo) {
            $data = [
                'id' => $dInfo['deviceId'],
                'mac' => $dInfo['mac'],
                'connect_protocol' => $this->config['connect_protocol'],
                'auth_key' => $this->config['auth_key'],
                'close_strategy' => $this->config['close_strategy'],
                'conn_strategy' => $this->config['conn_strategy'],
                'crypt_method' => $this->config['crypt_method'],
                'auth_ver' => $this->config['auth_ver'],
                'manu_mac_pos' => $this->config['manu_mac_pos'],
                'ser_mac_pos' => $this->config['ser_mac_pos'],
            ];

            !empty($this->config['ble_simple_protocol']) ? $data['ble_simple_protocol'] = $this->config['ble_simple_protocol'] : '';

            $res[] = $data;
        }

        return $res;
    }

    public function createDeviceId()
    {
        $params = [
            'product_id' => $this->productId,
        ];

        return $this->parseJSON('get', [self::API_DEV_GET_QRCODE, $params]);
    }

    public function bind($openId, $deviceId, $ticket)
    {
        $params = [
            'ticket' => $ticket,
            'device_id' => $deviceId,
            'openid' => $openId,
        ];

        return $this->parseJSON('json', [self::API_DEV_BIND, $params]);
    }

    public function unbind($openId, $deviceId, $ticket)
    {
        $params = [
            'ticket' => $ticket,
            'device_id' => $deviceId,
            'openid' => $openId,
        ];

        return $this->parseJSON('json', [self::API_DEV_UNBIND, $params]);
    }

    public function compelBind($openId, $deviceId)
    {
        $params = [
            'device_id' => $deviceId,
            'openid' => $openId,
        ];

        return $this->parseJSON('json', [self::API_DEV_COMPEL_BIND, $params]);
    }

    public function compelUnbind($openId, $deviceId)
    {
        $params = [
            'device_id' => $deviceId,
            'openid' => $openId,
        ];

        return $this->parseJSON('json', [self::API_DEV_COMPEL_UNBIND, $params]);
    }

    public function getDeviceStatus($deviceId)
    {
        $params = [
            'device_id' => $deviceId,
        ];

        return $this->parseJSON('get', [self::API_DEV_STAT, $params]);
    }

    public function verifyQrcode($ticket)
    {
        $params = [
            'ticket' => $ticket,
        ];

        return $this->parseJSON('post', [self::API_DEV_VERIFY_QRCODE, $params]);
    }

    public function getOpenid($deviceId)
    {
        $params = [
            'device_type' => $this->deviceType,
            'device_id' => $deviceId,
        ];

        return $this->parseJSON('get', [self::API_DEV_GET_OPENID, $params]);
    }

    public function getDeviceidByOpenid($openid)
    {
        $params = [
            'openid' => $openid,
        ];

        return $this->parseJSON('get', [self::API_USER_DEV_BIND, $params]);
    }
}
