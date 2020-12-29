<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\MicroMerchant;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\Kernel\Support;
use EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidSignException;

/**
 * Class Application.
 *
 * @author liuml <liumenglei0211@gmail.com>
 *
 * @property \EasyWeChat\MicroMerchant\Certficates\Client    $certficates
 * @property \EasyWeChat\MicroMerchant\Material\Client       $material
 * @property \EasyWeChat\MicroMerchant\MerchantConfig\Client $merchantConfig
 * @property \EasyWeChat\MicroMerchant\Withdraw\Client       $withdraw
 * @property \EasyWeChat\MicroMerchant\Media\Client          $media
 *
 * @method mixed submitApplication(array $params)
 * @method mixed getStatus(string $applymentId, string $businessCode = '')
 * @method mixed upgrade(array $params)
 * @method mixed getUpgradeStatus(string $subMchId = '')
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [
        // Base services
        Base\ServiceProvider::class,
        Certficates\ServiceProvider::class,
        MerchantConfig\ServiceProvider::class,
        Material\ServiceProvider::class,
        Withdraw\ServiceProvider::class,
        Media\ServiceProvider::class,
    ];

    /**
     * @var array
     */
    protected $defaultConfig = [
        'http' => [
            'base_uri' => 'https://api.mch.weixin.qq.com/',
        ],
        'log' => [
            'default' => 'dev', // 默认使用的 channel，生产环境可以改为下面的 prod
            'channels' => [
                // 测试环境
                'dev' => [
                    'driver' => 'single',
                    'path' => '/tmp/easywechat.log',
                    'level' => 'debug',
                ],
                // 生产环境
                'prod' => [
                    'driver' => 'daily',
                    'path' => '/tmp/easywechat.log',
                    'level' => 'info',
                ],
            ],
        ],
    ];

    /**
     * @return string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function getKey()
    {
        $key = $this['config']->key;

        if (empty($key)) {
            throw new InvalidArgumentException('config key connot be empty.');
        }

        if (32 !== strlen($key)) {
            throw new InvalidArgumentException(sprintf("'%s' should be 32 chars length.", $key));
        }

        return $key;
    }

    /**
     * set sub-mch-id and appid.
     *
     * @param string $subMchId Identification Number of Small and Micro Businessmen Reported by Service Providers
     * @param string $appId    Public Account ID of Service Provider
     *
     * @return $this
     */
    public function setSubMchId(string $subMchId, string $appId = '')
    {
        $this['config']->set('sub_mch_id', $subMchId);
        if ($appId) {
            $this['config']->set('appid', $appId);
        }

        return $this;
    }

    /**
     * setCertificate.
     *
     * @param string $certificate
     * @param string $serialNo
     *
     * @return $this
     */
    public function setCertificate(string $certificate, string $serialNo)
    {
        $this['config']->set('certificate', $certificate);
        $this['config']->set('serial_no', $serialNo);

        return $this;
    }

    /**
     * Returning true indicates that the verification is successful,
     * returning false indicates that the signature field does not exist or is empty,
     * and if the signature verification is wrong, the InvalidSignException will be thrown directly.
     *
     * @param array $data
     *
     * @return bool
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\MicroMerchant\Kernel\Exceptions\InvalidSignException
     */
    public function verifySignature(array $data)
    {
        if (!isset($data['sign']) || empty($data['sign'])) {
            return false;
        }

        $sign = $data['sign'];
        unset($data['sign']);

        $signType = strlen($sign) > 32 ? 'HMAC-SHA256' : 'MD5';
        $secretKey = $this->getKey();

        $encryptMethod = Support\get_encrypt_method($signType, $secretKey);

        if (Support\generate_sign($data, $secretKey, $encryptMethod) === $sign) {
            return true;
        }

        throw new InvalidSignException('return value signature verification error');
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this['base'], $name], $arguments);
    }
}
