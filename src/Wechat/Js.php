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
 * Js.php.
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace Overtrue\Wechat;

use Overtrue\Wechat\Utils\JSON;

/**
 * 微信 JSSDK.
 */
class Js
{
    /**
     * 应用ID.
     *
     * @var string
     */
    protected $appId;

    /**
     * 应用secret.
     *
     * @var string
     */
    protected $appSecret;

    /**
     * Cache对象
     *
     * @var Cache
     */
    protected $cache;

    /**
     * 当前URL.
     *
     * @var string
     */
    protected $url;

    const API_TICKET = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi';

    /**
     * constructor.
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->cache = new Cache($appId);
    }

    /**
     * 获取JSSDK的配置数组.
     *
     * @param array $APIs
     * @param bool  $debug
     * @param bool  $json
     *
     * @return string|array
     */
    public function config(array $APIs, $debug = false, $beta = false, $json = true)
    {
        $signPackage = $this->getSignaturePackage();
        $base = array(
                 'debug' => $debug,
                 'beta' => $beta,
                );
        $config = array_merge($base, $signPackage, array('jsApiList' => $APIs));

        return $json ? JSON::encode($config) : $config;
    }

    /**
     * 获取数组形式的配置.
     *
     * @param array $APIs
     * @param bool  $debug
     * @param bool  $beta
     *
     * @return array
     */
    public function getConfigArray(array $APIs, $debug = false, $beta = false)
    {
        return $this->config($APIs, $debug, $beta, false);
    }

    /**
     * 获取jsticket.
     *
     * @return string
     */
    public function getTicket()
    {
        $key = 'overtrue.wechat.jsapi_ticket.'.$this->appId;

        // for php 5.3
        $appId = $this->appId;
        $appSecret = $this->appSecret;
        $cache = $this->cache;
        $apiTicket = self::API_TICKET;

        return $this->cache->get(
            $key,
            function ($key) use ($appId, $appSecret, $cache, $apiTicket) {
                $http = new Http(new AccessToken($appId, $appSecret));

                $result = $http->get($apiTicket);

                $cache->set($key, $result['ticket'], $result['expires_in']);

                return $result['ticket'];
            }
        );
    }

    /**
     * 签名.
     *
     * @param string $url
     * @param string $nonce
     * @param int    $timestamp
     *
     * @return array
     */
    public function getSignaturePackage($url = null, $nonce = null, $timestamp = null)
    {
        $url = $url ? $url : $this->getUrl();
        $nonce = $nonce ? $nonce : $this->getNonce();
        $timestamp = $timestamp ? $timestamp : time();
        $ticket = $this->getTicket();

        $sign = array(
                 'appId' => $this->appId,
                 'nonceStr' => $nonce,
                 'timestamp' => $timestamp,
                 'url' => $url,
                 'signature' => $this->getSignature($ticket, $nonce, $timestamp, $url),
                );

        return $sign;
    }

    /**
     * 生成签名.
     *
     * @param string $ticket
     * @param string $nonce
     * @param int    $timestamp
     * @param string $url
     *
     * @return string
     */
    public function getSignature($ticket, $nonce, $timestamp, $url)
    {
        return sha1("jsapi_ticket={$ticket}&noncestr={$nonce}&timestamp={$timestamp}&url={$url}");
    }

    /**
     * 设置当前URL.
     *
     * @param string $url
     *
     * @return Js
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * 获取当前URL.
     *
     * @return string
     */
    public function getUrl()
    {
        if ($this->url) {
            return $this->url;
        }

        return Url::current();
    }

    /**
     * 获取随机字符串.
     *
     * @return string
     */
    public function getNonce()
    {
        return uniqid('rand_');
    }
}
